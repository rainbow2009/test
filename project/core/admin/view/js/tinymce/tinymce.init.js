


function MCEInit(element,height = 400) {
console.log (element ||  tinyMceDefaultAreas)
    tinymce.init({
        language: 'ru',
        mode:'exact',
        elements:element ||  tinyMceDefaultAreas,
        height:height,
        gecko_spellcheck:true,
        relative_urls:false,
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table directionality",
            "emoticons template paste textpattern media imagetools"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | forecolor backcolor emoticons | " +
            "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | " +
            "formatselect fontsizeselect | code media emoticons 
    })

}

MCEInit()