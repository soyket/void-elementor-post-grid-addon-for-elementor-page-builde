 jQuery( function( $ ) {
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {

        //get post type 
        $('[data-setting="post_type"]').change(function(){        
            $('[data-setting="taxonomy_type"]').empty();
            var post_type = $('[data-setting="post_type"]').val() || [];
            var data = {
                action: 'void_grid_ajax_tax',
                postTypeNonce : void_grid_ajax.postTypeNonce,
                post_type: post_type
            };        
            $.post(void_grid_ajax.ajaxurl, data, function(response) {        
                var taxonomy_name = JSON.parse(response);                 
                $.each(taxonomy_name,function(){
                    if(this.name == 'post_format'){
                        return;
                    }
                    $('[data-setting="taxonomy_type"]').append('<option value="'+this.name+'">'+this.name+'</option>'); 
                });
                $('[data-setting="taxonomy_type"]')[0].selectedIndex = -1;
            });
            return true;
        });
        $('[data-setting="taxonomy_type"]').change(function(){        
            $('[data-setting="terms"]')[0].options.length = 0;       
            var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
            var data = {
                action: 'void_grid_ajax_terms',
                postTypeNonce : void_grid_ajax.postTypeNonce,
                taxonomy_type: taxonomy_type
            };      
            $.post(void_grid_ajax.ajaxurl, data, function(response) {        
                var terms = JSON.parse(response);                        
                $.each(terms,function(){
                    $('[data-setting="terms"]').append('<option value="'+this.id+'">'+this.name+'</option>'); 
                });
                $('[data-setting="terms"]')[0].selectedIndex = -1;
            });   
            return true;
        });
    } );

});