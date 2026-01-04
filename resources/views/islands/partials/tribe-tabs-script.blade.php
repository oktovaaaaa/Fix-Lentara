{{-- resources/views/islands/partials/tribe-tabs-script.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs   = document.querySelectorAll('[data-tribe-tab]');
    const panels = document.querySelectorAll('[data-tribe-panel]');

    if (!tabs.length || !panels.length) return;

    function setTribe(name) {
        // tab active
        tabs.forEach(tab => {
            tab.classList.toggle('is-active', tab.dataset.tribeTab === name);
        });

        // panel show/hide
        panels.forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.tribePanel !== name);
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => setTribe(tab.dataset.tribeTab));
    });

    // default active: yang sudah punya class is-active
    const defaultTab = document.querySelector('[data-tribe-tab].is-active');
    if (defaultTab) {
        setTribe(defaultTab.dataset.tribeTab);
    } else {
        // fallback: tab pertama
        setTribe(tabs[0].dataset.tribeTab);
    }
});
</script>
