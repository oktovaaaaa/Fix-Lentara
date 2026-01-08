<?php

namespace App\Http\Controllers;

use App\Models\GameLevel;
use App\Models\PlayerIslandProgress;
use App\Models\PlayerLevelProgress;
use App\Models\Island;
use App\Models\Player; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * ==============
     * CONFIG
     * ==============
     */
    private const XP_PER_CORRECT = 2;        // 2 XP per soal benar
    private const QUESTIONS_PER_LEVEL = 5;  // 5 soal per level
    private const MIN_PASS = 3;             // minimal lulus 3 benar
    private const HEART_REFILL_COST = 10;   // isi ulang hati = 10 uang
    private const HEART_REGEN_SECONDS = 300; // 1 hati / 5 menit (realtime)

    /**
     * WARNA PULAU (WAJIB SESUAI REQUEST)
     */
    private array $islandColors = [
        'sumatera' => '#f97316',     // orange
        'jawa' => '#3b82f6',         // biru
        'kalimantan' => '#22c55e',   // hijau
        'sulawesi' => '#facc15',     // kuning

        // Sunda Kecil = hijau muda
        'sunda-kecil' => '#86efac',
        'sunda_kecil' => '#86efac',
        'sundakecil'  => '#86efac',

        // Papua & Maluku = coklat
        'papua-maluku' => '#a16207',
        'papua_maluku' => '#a16207',
        'papua&maluku' => '#a16207',
        'papua' => '#a16207',
        'maluku' => '#a16207',
    ];

    /**
     * TIER XP (BAHASA INDONESIA)
     */
    private array $tiers = [
        ['min' => 300, 'label' => 'Legenda'],
        ['min' => 100, 'label' => 'Pakar'],
        ['min' => 50,  'label' => 'Penjelajah'],
        ['min' => 25,  'label' => 'Pelatih'],
        ['min' => 10,  'label' => 'Pemula'],
        ['min' => 0,   'label' => '—'],
    ];

    /**
     * ============================
     * REGEN HATI REALTIME (SERVER)
     * ============================
     */
    private function syncHearts(Player $player): void
    {
        $max = (int) ($player->hearts_max ?? 5);
        $cur = (int) ($player->hearts ?? 0);

        if ($cur >= $max) {
            // jika full, pastikan hearts_updated_at diisi biar stabil
            if (!$player->hearts_updated_at) {
                $player->hearts_updated_at = Carbon::now();
                $player->save();
            }
            return;
        }

        $last = $player->hearts_updated_at ? Carbon::parse($player->hearts_updated_at) : Carbon::now();
        $now  = Carbon::now();

        if ($now->lessThanOrEqualTo($last)) {
            return;
        }

        $diff = $last->diffInSeconds($now);
        $add  = intdiv($diff, self::HEART_REGEN_SECONDS);

        if ($add <= 0) return;

        $new = min($max, $cur + $add);

        // majuin timestamp sesuai hati yang terisi
        $advanceSeconds = $add * self::HEART_REGEN_SECONDS;
        $player->hearts = $new;

        $player->hearts_updated_at = $last->copy()->addSeconds($advanceSeconds);

        // kalau sudah full, set ke now supaya regen stop rapi
        if ($new >= $max) {
            $player->hearts_updated_at = $now;
        }

        $player->save();
    }

    private function resolveTier(int $xp): string
    {
        foreach ($this->tiers as $t) {
            if ($xp >= $t['min']) return $t['label'];
        }
        return '—';
    }

    public function learn(Request $request)
    {
        $player = Auth::guard('player')->user();
        $this->syncHearts($player);
        $player->refresh();

        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $levels = GameLevel::query()
            ->whereIn('island_id', $islands->pluck('id'))
            ->where('is_active', true)
            ->orderBy('island_id')
            ->orderBy('order')
            ->get()
            ->groupBy('island_id');

        $levelProgress = PlayerLevelProgress::query()
            ->where('player_id', $player->id)
            ->get()
            ->keyBy('game_level_id');

        $islandProgress = PlayerIslandProgress::query()
            ->where('player_id', $player->id)
            ->get()
            ->keyBy('island_id');

        // unlock: pulau pertama terbuka, berikutnya terbuka kalau pulau sebelumnya completed
        $unlockedIslandIds = [];
        $prevCompleted = true;
        foreach ($islands as $island) {
            if ($prevCompleted) {
                $unlockedIslandIds[] = $island->id;
            }
            $prevCompleted = (bool) ($islandProgress[$island->id]->is_completed ?? false);
        }

        $tierLabel = $this->resolveTier((int)$player->xp_total);

        // pulau aktif dari query ?island=ID (kalau valid & unlocked), kalau tidak pakai first unlocked
        $activeIslandId = (int) ($request->query('island') ?? 0);
        $activeIsland = null;

        if ($activeIslandId > 0) {
            $candidate = $islands->firstWhere('id', $activeIslandId);
            if ($candidate && in_array($candidate->id, $unlockedIslandIds, true)) {
                $activeIsland = $candidate;
            }
        }
        if (!$activeIsland) {
            foreach ($islands as $isl) {
                if (in_array($isl->id, $unlockedIslandIds, true)) { $activeIsland = $isl; break; }
            }
        }
        if (!$activeIsland) $activeIsland = $islands->first();

        return view('player.learn.index', [
            'player' => $player,
            'islands' => $islands,
            'levels' => $levels,
            'levelProgress' => $levelProgress,
            'islandProgress' => $islandProgress,
            'unlockedIslandIds' => $unlockedIslandIds,
            'tierLabel' => $tierLabel,
            'activeIsland' => $activeIsland,
            'islandColors' => $this->islandColors,
        ]);
    }

    public function guide()
{
    $player = Auth::guard('player')->user();
    $this->syncHearts($player);
    $player->refresh();

    $tierLabel = $this->resolveTier((int)$player->xp_total);

    return view('player.learn.guide', [
        'player' => $player,
        'tierLabel' => $tierLabel,
        'islandColors' => $this->islandColors,
    ]);
}


    public function play(GameLevel $level)
    {
        $player = Auth::guard('player')->user();
        $this->syncHearts($player);
        $player->refresh();

        // kalau hati habis, jangan boleh main
        if ((int)$player->hearts <= 0) {
            return redirect()->route('game.learn')->with('hearts_empty', true);
        }

        // cek pulau unlocked
        if (!$this->isIslandUnlocked($player->id, $level->island_id)) {
            return redirect()->route('game.learn')->with('error', 'Pulau ini masih terkunci.');
        }

        // cek level unlocked (harus level pertama atau level sebelumnya completed)
        if (!$this->isLevelUnlocked($player->id, $level)) {
            return redirect()->route('game.learn')->with('error', 'Level ini masih terkunci.');
        }

        $questions = $level->questions()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // validasi minimal: harus 5 soal
        if ($questions->count() !== self::QUESTIONS_PER_LEVEL) {
            return redirect()->route('game.learn')->with('error', 'Level ini belum siap (harus 5 soal).');
        }

        // bersihkan run session lama biar start fresh
        session()->forget('game_run_'.$level->id);

        return view('player.learn.play', [
            'player' => $player,
            'level' => $level,
            'questions' => $questions,
            'islandColors' => $this->islandColors,
        ]);
    }

    /**
     * submit batch lama (tidak dipakai untuk flow baru).
     * Aku biarkan supaya route tidak error kalau ada yang masih manggil.
     */
    public function submit(Request $request, GameLevel $level)
    {
        return redirect()->route('game.play', $level->id);
    }

    /**
     * CHECK PER SOAL (AJAX)
     */
    public function check(Request $request, GameLevel $level)
    {
        $player = Auth::guard('player')->user();
        $this->syncHearts($player);
        $player->refresh();

        // kalau hati habis, stop
        if ((int)$player->hearts <= 0) {
            return response()->json([
                'ok' => false,
                'code' => 'HEARTS_EMPTY',
                'message' => 'Hati kamu habis.',
                'hearts' => (int)$player->hearts,
                'hearts_max' => (int)$player->hearts_max,
                'coins' => (int)$player->coins,
                'xp_total' => (int)$player->xp_total,
            ], 403);
        }

        // akses valid?
        if (!$this->isIslandUnlocked($player->id, $level->island_id) || !$this->isLevelUnlocked($player->id, $level)) {
            return response()->json(['ok' => false, 'message' => 'Akses tidak valid.'], 403);
        }

        $data = $request->validate([
            'question_id' => ['required','integer'],
            'type'        => ['required','in:mcq,fill'],
            'answer'      => ['required'],
        ]);

        $questions = $level->questions()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        if ($questions->count() !== self::QUESTIONS_PER_LEVEL) {
            return response()->json(['ok' => false, 'message' => 'Level belum siap (harus 5 soal).'], 422);
        }

        $question = $questions->firstWhere('id', (int)$data['question_id']);
        if (!$question) {
            return response()->json(['ok' => false, 'message' => 'Soal tidak ditemukan pada level ini.'], 404);
        }

        $runKey = 'game_run_'.$level->id;
        $run = session()->get($runKey, [
            'answered_ids' => [],
            'correct_count' => 0,
            'wrong_count' => 0,
            'xp_gained' => 0,
        ]);

        // cegah double check soal yang sama
        if (in_array((int)$question->id, $run['answered_ids'], true)) {
            return response()->json([
                'ok' => true,
                'duplicate' => true,
                'message' => 'Soal ini sudah pernah diperiksa.',
                'correct' => null,
                'xp_gained' => 0,
                'xp_total' => (int)$player->xp_total,
                'hearts' => (int)$player->hearts,
                'hearts_max' => (int)$player->hearts_max,
                'coins' => (int)$player->coins,
                'correct_count' => (int)$run['correct_count'],
                'wrong_count' => (int)$run['wrong_count'],
                'finished' => false,
                'passed' => false,
                'out_of_hearts' => ((int)$player->hearts <= 0),
            ]);
        }

        $isCorrect = false;

        if ($question->type === 'mcq') {
            $picked = strtoupper(trim((string)$data['answer']));
            $isCorrect = in_array($picked, ['A','B','C','D'], true) && $picked === $question->correct_option;
        } else {
            // fill: ignore case + trim spasi (case-insensitive + trim)
            $typed = (string)$data['answer'];
            $typed = strtolower(trim($typed));
            $typed = preg_replace('/\s+/', '', $typed);

            $truth = strtolower(trim((string)$question->correct_text));
            $truth = preg_replace('/\s+/', '', $truth);

            $truthLen = strlen((string)$truth);
            $truthLen = max(1, $truthLen);

            // wajib pas panjang & sama persis
            $isCorrect = ($typed !== '' && strlen((string)$typed) === $truthLen && $typed === $truth);
        }

        // tandai answered
        $run['answered_ids'][] = (int)$question->id;

        $xpGainedThis = 0;

        if ($isCorrect) {
            $run['correct_count'] = (int)$run['correct_count'] + 1;
            $xpGainedThis = self::XP_PER_CORRECT;

            $run['xp_gained'] = (int)$run['xp_gained'] + $xpGainedThis;
            $player->xp_total = (int)$player->xp_total + $xpGainedThis;
        } else {
            $run['wrong_count'] = (int)$run['wrong_count'] + 1;
            $player->hearts = max(0, (int)$player->hearts - 1);

            // kalau hati habis, jangan lupa set timestamp regen
            if ((int)$player->hearts <= 0) {
                $player->hearts_updated_at = Carbon::now();
            }
        }

        $player->save();
        session()->put($runKey, $run);

        $answeredCount = count((array)$run['answered_ids']);
        $finished = $answeredCount >= self::QUESTIONS_PER_LEVEL;

        $passed = false;
        $islandCompletedNow = false;
        $coinsRewarded = 0;

        // finalize level hanya kalau sudah 5 soal
        if ($finished) {
            $correctCount = (int)$run['correct_count'];
            $passed = $correctCount >= self::MIN_PASS;

            // aturan kamu: progress tidak disimpan kalau gagal
            if ($passed) {
                $prog = PlayerLevelProgress::firstOrCreate(
                    ['player_id' => $player->id, 'game_level_id' => $level->id],
                    ['best_correct' => 0, 'is_completed' => false]
                );

                if ($correctCount > (int)$prog->best_correct) {
                    $prog->best_correct = $correctCount;
                }

                if (!$prog->is_completed) {
                    $prog->is_completed = true;
                    $prog->completed_at = Carbon::now();
                }

                $prog->save();

                // cek pulau selesai → reward +20
                [$islandCompletedNow, $coinsRewarded] = $this->checkAndCompleteIsland($player->id, $level->island_id);
                if ($islandCompletedNow) {
                    $player->refresh();
                }
            }

            // clear run session supaya next play fresh
            session()->forget($runKey);
        }

        $outOfHearts = ((int)$player->hearts <= 0);

        return response()->json([
            'ok' => true,
            'duplicate' => false,
            'correct' => (bool)$isCorrect,
            'message' => $isCorrect ? 'Jawaban kamu benar.' : 'Jawaban kamu salah. Hati berkurang.',
            'xp_gained' => (int)$xpGainedThis,
            'xp_total' => (int)$player->xp_total,
            'hearts' => (int)$player->hearts,
            'hearts_max' => (int)$player->hearts_max,
            'coins' => (int)$player->coins,
            'correct_count' => (int)$run['correct_count'],
            'wrong_count' => (int)$run['wrong_count'],
            'answered_count' => (int)$answeredCount,
            'total_questions' => self::QUESTIONS_PER_LEVEL,
            'finished' => (bool)$finished,
            'passed' => (bool)$passed,
            'run_xp_gained' => (int)($run['xp_gained'] ?? 0),
            'out_of_hearts' => (bool)$outOfHearts,
            'island_completed_now' => (bool)$islandCompletedNow,
            'coins_rewarded' => (int)$coinsRewarded,
        ]);
    }

    /**
     * ISI ULANG HATI (10 uang) → full ke hearts_max
     */
    public function refillHearts(Request $request)
    {
        $player = Auth::guard('player')->user();
        $this->syncHearts($player);
        $player->refresh();

        $max = (int)($player->hearts_max ?? 5);

        if ((int)$player->hearts >= $max) {
            return response()->json([
                'ok' => true,
                'message' => 'Hati kamu sudah penuh.',
                'hearts' => (int)$player->hearts,
                'hearts_max' => (int)$player->hearts_max,
                'coins' => (int)$player->coins,
                'xp_total' => (int)$player->xp_total,
            ]);
        }

        if ((int)$player->coins < self::HEART_REFILL_COST) {
            return response()->json([
                'ok' => false,
                'code' => 'COINS_NOT_ENOUGH',
                'message' => 'Uang tidak cukup. Silahkan tunggu hati sampai penuh.',
                'hearts' => (int)$player->hearts,
                'hearts_max' => (int)$player->hearts_max,
                'coins' => (int)$player->coins,
                'xp_total' => (int)$player->xp_total,
            ], 422);
        }

        $player->coins = (int)$player->coins - self::HEART_REFILL_COST;
        $player->hearts = $max;
        $player->hearts_updated_at = Carbon::now();
        $player->save();

        return response()->json([
            'ok' => true,
            'message' => 'Hati berhasil diisi ulang.',
            'hearts' => (int)$player->hearts,
            'hearts_max' => (int)$player->hearts_max,
            'coins' => (int)$player->coins,
            'xp_total' => (int)$player->xp_total,
        ]);
    }

    private function isIslandUnlocked(int $playerId, int $islandId): bool
    {
        $islands = Island::query()->where('is_active', true)->orderBy('order')->get();

        $progress = PlayerIslandProgress::query()
            ->where('player_id', $playerId)
            ->get()
            ->keyBy('island_id');

        $prevCompleted = true;
        foreach ($islands as $island) {
            $unlocked = $prevCompleted;
            if ((int)$island->id === (int)$islandId) {
                return $unlocked;
            }
            $prevCompleted = (bool) ($progress[$island->id]->is_completed ?? false);
        }
        return false;
    }

    private function isLevelUnlocked(int $playerId, GameLevel $level): bool
    {
        $levels = GameLevel::query()
            ->where('island_id', $level->island_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $progress = PlayerLevelProgress::query()
            ->where('player_id', $playerId)
            ->whereIn('game_level_id', $levels->pluck('id'))
            ->get()
            ->keyBy('game_level_id');

        $prevCompleted = true;
        foreach ($levels as $lv) {
            $unlocked = $prevCompleted;
            if ((int)$lv->id === (int)$level->id) {
                return $unlocked;
            }
            $prevCompleted = (bool) ($progress[$lv->id]->is_completed ?? false);
        }
        return false;
    }

    /**
     * return [bool completedNow, int coinsRewarded]
     */
    private function checkAndCompleteIsland(int $playerId, int $islandId): array
    {
        $levels = GameLevel::query()
            ->where('island_id', $islandId)
            ->where('is_active', true)
            ->pluck('id');

        if ($levels->isEmpty()) return [false, 0];

        $doneCount = PlayerLevelProgress::query()
            ->where('player_id', $playerId)
            ->whereIn('game_level_id', $levels)
            ->where('is_completed', true)
            ->count();

        if ($doneCount !== $levels->count()) return [false, 0];

        $isProg = PlayerIslandProgress::firstOrCreate(
            ['player_id' => $playerId, 'island_id' => $islandId],
            ['is_completed' => false]
        );

        if ($isProg->is_completed) return [false, 0];

        $isProg->is_completed = true;
        $isProg->completed_at = Carbon::now();
        $isProg->save();

        // reward coins +20
        $player = Player::find($playerId);
        if ($player) {
            $player->coins = (int)$player->coins + 20;
            $player->save();
        }

        return [true, 20];
    }
}
