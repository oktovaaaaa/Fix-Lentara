{{-- resources/views/islands/partials/tribe-tabs-script.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs   = document.querySelectorAll('[data-tribe-tab]');
        const panels = document.querySelectorAll('[data-tribe-panel]');

        function setTribe(name) {
            // ganti style tab
            tabs.forEach(tab => {
                if (tab.dataset.tribeTab === name) {
                    tab.classList.add('is-active');
                } else {
                    tab.classList.remove('is-active');
                }
            });

            // tampilkan konten sesuai suku
            panels.forEach(panel => {
                if (panel.dataset.tribePanel === name) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                setTribe(tab.dataset.tribeTab);
            });
        });

        // default suku pertama – tiap pulau bisa override lewat data-atribut kalau mau
        // di Sumatera: 'aceh', di Jawa nanti bisa 'sunda', dll
        const defaultTab = document.querySelector('[data-tribe-tab].is-active');
        if (defaultTab) {
            setTribe(defaultTab.dataset.tribeTab);
        }
    });
</script>
