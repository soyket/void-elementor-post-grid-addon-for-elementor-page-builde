//Ajax Filtering
(function($, $W) {
    'use strict';


    var VOID_ELEMENTOR_AJAX_FILTERS = {
        //---Default values :
        _is_first_load : 1,
        _filtered_terms : [],
        _data_to_check : [], //will be pop when DOM is ready ... according VOID_ELEMENTOR__JS_GLOBALS

        _filtered_post_types : [], //TODO
        _post_types_option_enable_all_is_available : 1, //TODO
        _post_types_filters_all_checked : 1, //TODO
        //---!Default value

        _load_posts_filtered : function (paged) {

            var paged_value = paged; //Store the paged value if it's being sent through when the function is called
            var ajax_url = void_grid__js__params.ajax_url; //Get ajax URL (added through wp_localize_script)
            if(VOID_ELEMENTOR_AJAX_FILTERS._is_first_load){
                VOID_ELEMENTOR_AJAX_FILTERS._post_types_filters_all_checked = (typeof void_grid__js__params._post_types_filters_all_checked != 'undefined' ) ? void_grid__js__params._post_types_filters_all_checked : '0';
            }

            VOID_ELEMENTOR_AJAX_FILTERS.updateSelectedPostTypes();
            VOID_ELEMENTOR_AJAX_FILTERS.updateSelectedTerms();
            // console.log(VOID_ELEMENTOR__JS_GLOBALS);
            // console.log(VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms);

            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: {
                    action: 'display_filtered_posts',
                    settings : VOID_ELEMENTOR__JS_GLOBALS.settings,
                    settings_dynamic : {
                        terms_selected: VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms,
                        post_types_selected: VOID_ELEMENTOR_AJAX_FILTERS._filtered_post_types,
                        paged: paged_value
                    },
                    // is_first_load : VOID_ELEMENTOR_AJAX_FILTERS._is_first_load,
                    // post_types_filters_all_checked : VOID_ELEMENTOR_AJAX_FILTERS._post_types_filters_all_checked,
                    // post_types_option_enable_all_is_available : VOID_ELEMENTOR_AJAX_FILTERS._post_types_option_enable_all_is_available,

                    // display_type : VOID_ELEMENTOR__JS_GLOBALS.settings.display_type,
                    // posts_per_row : VOID_ELEMENTOR__JS_GLOBALS.settings.posts_per_row,
                    // post_count : VOID_ELEMENTOR__JS_GLOBALS.settings.posts,
                    // taxonomy_type : VOID_ELEMENTOR__JS_GLOBALS.settings.taxonomy_type,
                    // pagination_yes : VOID_ELEMENTOR__JS_GLOBALS.settings.pagination_yes,
                    // ajax_enabled : VOID_ELEMENTOR__JS_GLOBALS.settings.pagination_yes,
                },
                beforeSend: function ()
                {
                    $('.ajax-post-loader img').fadeIn("slow");
                },
                success: function(result_markup)
                {
                    VOID_ELEMENTOR_AJAX_FILTERS._is_first_load = 0;
                    $('#void_elementor-filter_posts_results').html(result_markup);
                    // $('#void_elementor-ajax').html(result_markup);
                    // $('#void_elementor-filter_posts_results').html(result_markup);
                    if(void_grid__js__params.slick_top_post && $('#page_posts__top_post__images', result_markup).length){
                        $('#page_posts__top_post__images').slick({
                            infinite: true,
                            // autoplay: true,
                            // autoplaySpeed: 2000,
                            useTransforms: true,
                        });
                    }

                    $('.ajax-post-loader img').fadeOut("slow");

                },
                error: function()
                {
                    //If an ajax error has occured, do something here...
                    $("#void_elementor-ajax_filter_posts_results").html('<p>Rien ici!</p>');
                }
            });
        },

        _change__filters : function() {
            if(!VOID_ELEMENTOR_AJAX_FILTERS._is_first_load){
                var $_clicked_element = $(this);

                $.each(VOID_ELEMENTOR_AJAX_FILTERS._data_to_check, function (index, selector) { //index is not used, just 'selector' is used

                    if (typeof $_clicked_element.data(selector) != 'undefined') {
                        var _value = $_clicked_element.data(selector),
                            is_checked = false; //set 'is_checked' value to synchronize others elements in same page for same value

                        switch($_clicked_element.data('void_elementor_exclusive')){
                            //"checked + class" for checkboxes and spans
                            case 'unique':
                                //Disable others and enable elements with our "value" (may be several same filters in the same page)
                                $('[data-' + selector + ']').prop('checked', false).removeClass('selected');
                                $('[data-' + selector + '="' + _value + '"]').prop('checked', true).addClass('selected');
                                break;
                            case 'multiple' :
                                if (typeof $_clicked_element.prop('checked') != 'undefined') {
                                    is_checked = $_clicked_element.prop('checked');
                                }
                                else{
                                    $_clicked_element.toggleClass('selected');
                                    is_checked = $_clicked_element.hasClass('selected');
                                }

                                //synchronize other filters of same data in the page (in case of same filters displayed several times)
                                $('[data-' + selector + '="' + _value + '"]').not($_clicked_element).prop('checked', is_checked).toggleClass('selected', is_checked);

                                //state of 'enable-all'
                                if(is_checked){
                                    //'enable-all' statement
                                    var _all_are_selected = $('[data-' + selector + ']:checked:enabled, [data-' + selector + '].selected').length == $('[data-' + selector + ']').not('[data-' + selector + '="enable-all"]').length;
                                    $('[data-' + selector + '="enable-all"]').prop('checked', _all_are_selected).toggleClass('selected', _all_are_selected);
                                }
                                else{
                                    $('[data-' + selector + '="enable-all"]').prop('checked', is_checked).toggleClass('selected', is_checked);
                                }
                                break;
                        }

                        //enable all elements of same data
                        if(_value == 'enable-all') {
                            $('[data-' + selector + ']').prop('checked', true).addClass('selected');
                        }

                    }
                });
            }

            VOID_ELEMENTOR_AJAX_FILTERS._load_posts_filtered(); //Load Posts
        },

        //Find Selected Post types
        updateSelectedPostTypes : function()
        {
            if(VOID_ELEMENTOR__JS_GLOBALS.settings.post_type_front_filters_yes == 'yes') {
                VOID_ELEMENTOR_AJAX_FILTERS._filtered_post_types = [];

                $('[data-void_elementor_ajax_filter_post_types]:checked, [data-void_elementor_ajax_filter_post_types].selected').each(function () {
                    VOID_ELEMENTOR_AJAX_FILTERS._filtered_post_types.push($(this).data('void_elementor_ajax_filter_post_types')); //Push value onto array
                });
            }
        },


        updateSelectedTerms : function ()
        {
            if(VOID_ELEMENTOR__JS_GLOBALS.settings.terms_front_filters_yes == 'yes') {
                VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms = [];
                $('[data-void_elementor_ajax_filter_terms]:checked, [data-void_elementor_ajax_filter_terms].selected').each(function () {
                    VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms.push($(this).data('void_elementor_ajax_filter_terms')); //Push value onto array
                });
            }
        },

        updatePagination : function ()
        {
            if(VOID_ELEMENTOR__JS_GLOBALS.settings.pagination_yes.length && VOID_ELEMENTOR__JS_GLOBALS.settings.pagination_yes == 'yes') {
                VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms = [];
                $('[data-void_elementor_ajax_filter_terms]:checked, [data-void_elementor_ajax_filter_terms].selected').each(function () {
                    VOID_ELEMENTOR_AJAX_FILTERS._filtered_terms.push($(this).data('void_elementor_ajax_filter_terms')); //Push value onto array
                });
            }
        },
    };





















    $(document).ready(function() { // Shorthand for $( document ).ready()

        //#######
        // Main ajax function
        //#######
        if (typeof VOID_ELEMENTOR__JS_GLOBALS != 'undefined' && VOID_ELEMENTOR__JS_GLOBALS.settings.ajax_enabled) {

            //pop VOID_ELEMENTOR_AJAX_FILTERS._data_to_check
            if (typeof VOID_ELEMENTOR__JS_GLOBALS.settings.post_type_front_filters_yes  != 'undefined' && VOID_ELEMENTOR__JS_GLOBALS.settings.post_type_front_filters_yes == 'yes') {
                VOID_ELEMENTOR_AJAX_FILTERS._data_to_check.push('void_elementor_ajax_filter_post_types');
            }

            if (typeof VOID_ELEMENTOR__JS_GLOBALS.settings.terms_front_filters_yes != 'undefined' && VOID_ELEMENTOR__JS_GLOBALS.settings.terms_front_filters_yes == 'yes') {
                VOID_ELEMENTOR_AJAX_FILTERS._data_to_check.push('void_elementor_ajax_filter_terms');
            }

            if (typeof VOID_ELEMENTOR__JS_GLOBALS.settings.post_type != 'undefined') {
                VOID_ELEMENTOR_AJAX_FILTERS._filtered_post_types.push(VOID_ELEMENTOR__JS_GLOBALS.settings.post_type);
            }

            VOID_ELEMENTOR_AJAX_FILTERS._change__filters();


            //###############
            //EVENT LISTENERS
            //###############

            //If list item is clicked, trigger
            // - checkboxes 'change'
            // - links 'click' : add class to flag as enable
            $.each(VOID_ELEMENTOR_AJAX_FILTERS._data_to_check, function (index, selector) {
                $('input[type="checkbox"][data-' + selector + ']').live('change', VOID_ELEMENTOR_AJAX_FILTERS._change__filters);
                $('span[data-' + selector + ']').live('click', VOID_ELEMENTOR_AJAX_FILTERS._change__filters);
            });

            //If pagination is clicked, load correct posts
            $('#void_pagination .page-numbers').live('click', function (e) {
                e.preventDefault();

                //var regExp = /\/page\/([^\/]+)\//; //format : /page/x/
                var regExp = /\/page\/([^\/]+)/; //format : /page/x
                var matches = regExp.exec($(this).attr('href'));
                var paged = (typeof matches == 'undefined') ? '' : ( matches != null ? matches[1] : '' );

                VOID_ELEMENTOR_AJAX_FILTERS._load_posts_filtered(paged); //Load Posts (feed in paged value)

            });

        }
    });



}(jQuery, jQuery(window)));