<div class="flex justify-center mt-8 space-x-6">
    @forelse ($socialLinks as $socialLink)
        <a href="{{ $socialLink->url }}" target="_blank" class="text-4xl text-gray-400 hover:text-red-400">
            <span class="{{ $socialLink->icon ? 'sr-only' : '' }} ">{{ $socialLink->nombre }}</span>
            <i class=" {{ $socialLink->icon}}"></i>
        </a>
    @empty
        <h3 class="test-gray-400">{{ _('There is no social links to show') }}</h3>
    @endforelse

    <!-- Boton add and edit -->

    <!-- SlideOver -->
</div>