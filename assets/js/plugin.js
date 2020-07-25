// -----------------------------

//   js index
/* =================== */
/*  
    

    

*/
// -----------------------------


(function($) {
    "use strict";

    console.log('loaded plugin.js');

    /*---------------------
    preloader
    --------------------- */

    $(window).on('load', function() {
        $('#preloader').fadeOut('slow', function() { $(this).remove(); });
    });


    // Grid Shuffle Style 1 
    if ($('.void-shuffle-grid-1').length > 0) {
        var Shuffle = window.Shuffle;
        var myShuffle = new Shuffle(document.querySelector('.void-shuffle-grid-1'), {
        itemSelector: '.grid1-item',
        sizer: '.grid1-sizer',
        buffer: 1,
        });

        $('input[name="shuffle-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShuffle.filter(input.value);
        }
        });
        
    }
   

    // Grid Shuffle Style 2 
    if ($('.void-shuffle-grid-2').length > 0) {
        var Shuffle = window.Shuffle;
        var myShuffle2 = new Shuffle(document.querySelector('.void-shuffle-grid-2'), {
        itemSelector: '.grid2-item',
        sizer: '.grid2-sizer',
        buffer: 1,
        });

        $('input[name="shuffle2-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShuffle2.filter(input.value);
        }
        });
        
    }


    // List Shuffle Style 1 
    if ($('.void-shuffle-list-1').length > 0) {
        var Shuffle = window.Shuffle;
        var myShufflelist1 = new Shuffle(document.querySelector('.void-shuffle-list-1'), {
        itemSelector: '.list1-item',
        sizer: '.list1-sizer',
        buffer: 1,
        });

        $('input[name="shuffle-list1-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShufflelist1.filter(input.value);
        }
        });
        
    }



    // Grid Rounded Shuffle Style 3 
    if ($('.void-shuffle-grid-3').length > 0) {
        var Shuffle = window.Shuffle;
        var myShuffle3 = new Shuffle(document.querySelector('.void-shuffle-grid-3'), {
        itemSelector: '.grid3-item',
        sizer: '.grid3-sizer',
        buffer: 1,
        });

        $('input[name="shuffle4-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShuffle3.filter(input.value);
        }
        });
        
    }

    // Grid Rounded Shuffle Style 4
    if ($('.void-shuffle-grid-4').length > 0) {
        var Shuffle = window.Shuffle;
        var myShuffle4 = new Shuffle(document.querySelector('.void-shuffle-grid-4'), {
        itemSelector: '.grid4-item',
        sizer: '.grid4-sizer',
        buffer: 1,
        });

        $('input[name="shuffle5-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShuffle4.filter(input.value);
        }
        });
        
    }




    // List Rounded Shuffle Style 2 
    if ($('.void-shuffle-list-2').length > 0) {
        var Shuffle = window.Shuffle;
        var myShuffle5 = new Shuffle(document.querySelector('.void-shuffle-list-2'), {
        itemSelector: '.list2-item',
        sizer: '.list2-sizer',
        buffer: 1,
        });

        $('input[name="shuffle6-filter"]').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) {
            myShuffle5.filter(input.value);
        }
        });
        
    }

    //Click TO Move Suffle active Filter Button 


    $('.shuffle-filter-btn .btn').on('click', function(){
        $('.shuffle-filter-btn .btn ').removeClass('active');
        $(this).addClass('active');
        // alert('Hellp');
    });

    var init = function( $scope, $ ) {
        console.log($scope);
    };

    // inilialization of js hook on elementor frondend and editor panel
    $(window).on('elementor/frontend/init', function () {

        // call the initialization function after loading elementor editor
        elementorFrontend.hooks.addAction( 'frontend/element_ready/void-post-grid.default', init);
        
        // call the the functionality of add, edit form when elementor editor panel is open for edit
        //elementor.hooks.addAction('panel/open_editor/widget/void-section-cf7', addEditActionFunction);

    });

}(jQuery));
