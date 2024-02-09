{if  isset($product)}
{assign var="allCateg" value=Product::getProductCategories($product.id_product)}
    {if in_array($id_categ, $allCateg)}
        <div class="row">
            <h3>{l s='L\'achat de ce produit exige une ordonnance.'}</h3>
            <div class="product-ordonnance">
                <form class="form" method="POST" enctype="multipart/form-data">
                    <p id="ordo-file">{l s='Déposez les fichiers à télécharger ou '} <input type="file" placeholder="TÉLÉCHARGER" id="ordonnace_file" name="ordonnace_file" /></p>
                </form>
            </div>

        </div>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script>
        var file_name = "";
        $('#ordonnace_file').on('change', function () {
            var fileInput = $(this)[0];
            var file = fileInput.files[0];
            var formData = new FormData();

            formData.append('file', file);
            $.ajax({
                processData: false,
                contentType: false,
                type: "POST",
                url: prestashop.urls.base_url + 'modules/uploadfile/ajax.php',
                data: formData,
                success: function (response) {
                    if($('.ordo-message-success').length > 0){
                        $('.ordo-message-success').replaceWith('<span class="ordo-message-success">'+response.message+'</span>')
                    }else if($('.ordo-message-error').length > 0){
                        $('.ordo-message-error').replaceWith('<span class="ordo-message-success">'+response.message+'</span>')
                    }else{
                        $('#ordo-file').append('<span class="ordo-message-success">'+response.message+'</span>')
                    }
                    console.log(response.file)
                    $('#ordo-name').val(response.file)
                    file_name = response.file
                    $('button.add-to-cart').show();
                    $('p.notpossible_to_cart').hide();
                },
                error: function (response) {
                    if($('.ordo-message-error').length > 0){
                        $('.ordo-message-error').replaceWith('<span class="ordo-message-error">'+response.responseText+'</span>')
                    }else if($('.ordo-message-success').length > 0){
                        $('.ordo-message-success').replaceWith('<span class="ordo-message-error">'+response.responseText+'</span>')
                    }else{
                        $('#ordo-file').append('<span class="ordo-message-error">'+response.responseText+'</span>')
                    }
                }
            });
        })
        </script>
    {/if}
{/if}