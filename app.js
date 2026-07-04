$(document).ready(function() {
    // 1. Live character counter for inputs with max length 100
    $('input[type="text"], input[type="password"], input[type="email"]').each(function() {
        var $input = $(this);
        if(!$input.attr('maxlength')) {
            $input.attr('maxlength', 100);
        }
        var $counter = $('<div class="char-counter">100 characters remaining</div>');
        $input.after($counter);

        $input.on('input', function() {
            var remaining = 100 - $(this).val().length;
            $counter.text(remaining + ' characters remaining');
            if (remaining < 10) {
                $counter.addClass('text-danger');
            } else {
                $counter.removeClass('text-danger');
            }
        });
    });

    // 2. Table row hover highlighting
    $('table tbody tr').on('mouseover', function() {
        $(this).addClass('hover-highlight');
    }).on('mouseout', function() {
        $(this).removeClass('hover-highlight');
    });

    // 3. Client-side confirmation prompt before deleting a record
    $('.delete-btn').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this record?')) {
            e.preventDefault();
        }
    });

    // 4. Fade-in animation for Bootstrap alert messages
    $('.alert').hide().fadeIn(1000);
    setTimeout(function() {
        $('.alert').fadeOut(1000, function() {
            $(this).remove();
        });
    }, 5000);

    // 5. Intersection Observer for Scroll Animations
    const scrollElements = document.querySelectorAll('.animate-on-scroll');
    const elementInView = (el, dividend = 1) => {
        const elementTop = el.getBoundingClientRect().top;
        return (elementTop <= (window.innerHeight || document.documentElement.clientHeight) / dividend);
    };

    const displayScrollElement = (element) => {
        element.classList.add('is-visible');
    };

    const handleScrollAnimation = () => {
        scrollElements.forEach((el) => {
            if (elementInView(el, 1.15)) {
                displayScrollElement(el);
            }
        });
    };

    // Initial check and scroll event listener
    if (scrollElements.length > 0) {
        handleScrollAnimation();
        window.addEventListener('scroll', () => {
            handleScrollAnimation();
        });
    }

    // Navbar tap animation
    $('.nav-link').on('click', function(){
      $(this).animate({ fontSize: '1.2rem' }, 150)
             .animate({ fontSize: '1rem' }, 150);
    });

    // Card bounce effect on tap
    $('.card').on('touchstart', function(){
      $(this).animate({ marginTop: "-5px" }, 200);
    }).on('touchend', function(){
      $(this).animate({ marginTop: "0px" }, 200);
    });

    // Announcement badge sparkle loop
    setInterval(function(){
      $('.badge').fadeOut(400).fadeIn(400);
    }, 2000);
});
