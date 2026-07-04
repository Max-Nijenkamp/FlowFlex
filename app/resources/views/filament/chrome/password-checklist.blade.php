{{-- Live password-requirements checklist. Watches the New password input by
     id (survives Livewire morphs — same element). Hidden until the field is
     focused or holds a value; slides open via the grid-rows trick. Pure
     client-side; server rules stay authoritative. --}}
<div
    class="ff-pw-checks-wrp"
    x-data="{
        v: '',
        focused: false,
        rules: [
            { label: 'At least 12 characters', test: (v) => v.length >= 12 },
            { label: 'Lower case letter (a–z)', test: (v) => /[a-z]/.test(v) },
            { label: 'Upper case letter (A–Z)', test: (v) => /[A-Z]/.test(v) },
            { label: 'A number (0–9)', test: (v) => /\d/.test(v) },
            { label: 'A symbol (!@#…)', test: (v) => /[^A-Za-z0-9]/.test(v) },
        ],
    }"
    x-init="
        const input = document.getElementById('ff-new-password')
        if (input) {
            v = input.value
            input.addEventListener('input', (e) => v = e.target.value)
            input.addEventListener('focus', () => focused = true)
            input.addEventListener('blur', () => focused = false)
        }
    "
    x-bind:class="{ 'ff-open': focused || v !== '' }"
>
    <ul class="ff-pw-checks">
        <template x-for="rule in rules" :key="rule.label">
            <li class="ff-pw-check" x-bind:class="{ 'ff-ok': rule.test(v) }">
                <svg x-show="rule.test(v)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="13" height="13"><path d="M4.5 10.5l3.5 3.5 7.5-8"></path></svg>
                <svg x-show="! rule.test(v)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" width="13" height="13"><circle cx="10" cy="10" r="6.5"></circle></svg>
                <span x-text="rule.label"></span>
            </li>
        </template>
    </ul>
</div>
