(function($) {
    "use strict";
    
    $(window).on('load', function() {
        $('#preloader').fadeOut('slow', function() { $(this).remove(); });
    });
    
    var init = function( $scope, $ ) {

        function shuffle(shuffle) {
            var cnt = 0;
            var initialInput = $scope.find('input[name="vepg-shuffle-filter"]:first');
            var allFilter = $scope.find('input.void-shuffle-all-filter');

            if(allFilter.length == 0){
                var parent = initialInput.parent();
                if( parent.hasClass('active') && (cnt <= 0) ){
                    activeFilter(initialInput);
                    cnt++;
                }
            }

            $scope.find('input[name="vepg-shuffle-filter"]').on('change', function (evt) {
                activeFilter(evt);
            });
            
            function activeFilter(evt){
                var input = evt.currentTarget;
                if(allFilter.length == 0 && cnt == 0){
                    var input = evt[0];
                }
                if (input.checked) {
                    shuffle.filter(input.value);
                }
            }            
        }
        // Grid Shuffle Style 1 
        if ($scope.find('.void-elementor-post-grid-grid-1').length > 0) {
            var Shuffle = window.Shuffle;
            var gridShuffle = new Shuffle($scope.find('.void-elementor-post-grid-grid-1'), {
            itemSelector: '.grid-item',
            sizer: '.filter-sizer',
            buffer: 1,
            });

            shuffle(gridShuffle);
            
        }
        // grid shuffle style 2
        if ($scope.find('.void-elementor-post-grid-minimal').length > 0) {
            var Shuffle = window.Shuffle;
            var gridShuffle2 = new Shuffle($scope.find('.void-elementor-post-grid-minimal'), {
            itemSelector: '.minimal-grid',
            sizer: '.filter-sizer',
            buffer: 1,
            });

            shuffle(gridShuffle2);
            
        }

        // List Shuffle Style 1 
        if ($scope.find('.void-elementor-post-grid-list-1').length > 0) {
            var Shuffle = window.Shuffle;
            var listShuffle = new Shuffle($scope.find('.void-elementor-post-grid-list-1'), {
            itemSelector: '.list-item',
            sizer: '.filter-sizer',
            buffer: 1,
            });

            shuffle(listShuffle);
            
        }

        //Click TO Move Suffle active Filter Button
        $scope.find('.void-elementor-post-grid-shuffle-btn .btn').on('click', function(){
            $scope.find('.void-elementor-post-grid-shuffle-btn .btn').removeClass('active');
            $(this).addClass('active');
            // alert('Hellp');
        });
    };

    // inilialization of js hook on elementor frondend and editor panel
    $(window).on('elementor/frontend/init', function () {

        // call the initialization function after loading elementor editor
        elementorFrontend.hooks.addAction( 'frontend/element_ready/void-post-grid.default', init);

    });

}(jQuery));
