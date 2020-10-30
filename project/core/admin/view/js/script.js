document.querySelector('.sitemap-button').onclick = () =>{
    Ajax({type: 'POST'})
        .then((res) => {
            console.log('good - '+res)
        })
        .catch((res)=>{
            console.log('bad - '+res)
        });
}
