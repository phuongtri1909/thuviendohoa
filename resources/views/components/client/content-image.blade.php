@props([
    'imageSrc' => '',
    'imageAlt' => '',
    'buttonText' => '> Xem thêm',
    'positionX' => '50%',
    'positionY' => '50%',
    'buttonClass' => 'px-3 py-2',
])

@push('styles')
    <style>
        /* Content Image Wrapper */
        .content-image-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .view-more-btn {
            border-radius: 0 20px 20px 20px;
            border: 1px solid var(--primary-color-5);
            background: #FFF;
            color: var(--primary-color-5);
            font-size: var(--font-size-md);
            font-weight: 700;
            position: absolute;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .view-more-btn[data-position-x][data-position-y] {
            left: var(--position-x, 50%);
            top: var(--position-y, 50%);
            transform: translate(-50%, -50%);
        }

        /* Responsive for view-more button */
        @media (max-width: 1200px) {
            .view-more-btn {
                font-size: 14px;
                padding: 6px 12px;
            }
        }

        @media (max-width: 992px) {
            .view-more-btn {
                font-size: 13px;
                padding: 5px 10px;
            }
        }

        @media (max-width: 768px) {
            .view-more-btn {
                font-size: 12px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 576px) {
            .view-more-btn {
                font-size: 11px;
                padding: 3px 6px;
            }
        }

        @media (max-width: 400px) {
            .view-more-btn {
                font-size: 10px;
                padding: 2px 4px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setButtonPosition() {
                const viewMoreButtons = document.querySelectorAll(
                    '.view-more-btn[data-position-x][data-position-y]');

                viewMoreButtons.forEach(button => {
                    let positionX = button.getAttribute('data-position-x');
                    let positionY = button.getAttribute('data-position-y');

                    // Responsive position adjustments
                    const screenWidth = window.innerWidth;

                    if (screenWidth <= 400) {
                        // Mobile small: Adjust position to avoid edges
                        const x = parseFloat(positionX);
                        const y = parseFloat(positionY);

                        // Keep button away from edges on small screens
                        if (x < 20) positionX = '20%';
                        if (x > 80) positionX = '80%';
                        if (y < 15) positionY = '15%';
                        if (y > 85) positionY = '85%';
                    } else if (screenWidth <= 576) {
                        // Mobile: Slight adjustment
                        const x = parseFloat(positionX);
                        const y = parseFloat(positionY);

                        if (x < 15) positionX = '15%';
                        if (x > 85) positionX = '85%';
                        if (y < 10) positionY = '10%';
                        if (y > 90) positionY = '90%';
                    } else if (screenWidth <= 768) {
                        // Tablet: Minor adjustment
                        const x = parseFloat(positionX);
                        const y = parseFloat(positionY);

                        if (x < 10) positionX = '10%';
                        if (x > 90) positionX = '90%';
                    }

                    // Set CSS custom properties
                    button.style.setProperty('--position-x', positionX);
                    button.style.setProperty('--position-y', positionY);
                });
            }

            // Set initial position
            setButtonPosition();

            // Update position on window resize
            window.addEventListener('resize', setButtonPosition);
        });
    </script>
@endpush


<div class="content-image-wrapper">
    <img src="{{ $imageSrc }}" alt="{{ $imageAlt }}" class="img-fluid">
    <button class="view-more-btn {{ $buttonClass }}" data-position-x="{{ $positionX }}"
        data-position-y="{{ $positionY }}">
        {{ $buttonText }}
    </button>
</div>
