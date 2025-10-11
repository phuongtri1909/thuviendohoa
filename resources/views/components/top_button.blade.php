<button id="topButton" class="btn bg-white px-2 py-4 pb-5 border-1 rounded-5 position-fixed bottom-0 end-0 m-4" 
        style="display: none; z-index: 1000;">
    <img src="{{ asset('images/svg/arrow-up.svg') }}" alt="Arrow Up">
</button>

<style>
    #topButton {
        width: 45px;
        height: 45px;
        transition: all 0.3s ease; 
        border: 1px solid #8E8E8E;  
    }
    
    #topButton:hover {
        opacity: 1;
        transform: translateY(-5px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topButton = document.getElementById('topButton');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                topButton.style.display = 'block';
            } else {
                topButton.style.display = 'none';
            }
        });
        
        topButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>