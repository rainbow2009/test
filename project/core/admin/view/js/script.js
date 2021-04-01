document.querySelector('.sitemap-button').onclick = (e) => {
    e.preventDefault();
    createSiteMap();
}
let links_counter = 0;

function createSiteMap() {
    links_counter++;
    Ajax({data: {ajax: 'sitemap', links_counter: links_counter}})
        .then((res) => {
            console.log('good - ' + res);
        })
        .catch((res) => {
            console.log('bad - ' + res);
            createSiteMap();
        });
}

let files = document.querySelectorAll('input[type=file]')

let fileStore = [];

if (files.length) {

    files.forEach(item => {

        item.onchange = function () {

            let multiple = false
            let parentContainer

            let container

            if (item.hasAttribute('multiple')) {

                multiple = true

                parentContainer = this.closest('.gallery_container')

                if (!parentContainer) {
                    return false
                }

                container = parentContainer.querySelectorAll('.empty_container')
                console.log(container.length)

                if (container.length < this.files.length) {

                    for (let index = 0; index < this.files.length - container.length; index++) {

                        let el = document.createElement('div')

                        el.classList.add('vg-dotted-square', 'g-center', 'empty_container')

                        parentContainer.append(el)
                    }
                    container = parentContainer.querySelectorAll('.empty_container')

                }
            }

            let fileName = item.name

            let attributeName = item.name.replace(/[\[\]]g/, '')

            for (let i in this.files) {

                if (this.files.hasOwnProperty(i)) {

                    if (multiple) {

                    } else {

                        container = this.closest('.img_container').querySelector('.img_show')
                        showImage(this.files[i], container)
                    }

                }
            }
        }
    })
}

function showImage(item, container) {

    let reader = new FileReader()

    container.innerHTML = '';

    reader.readAsDataURL(item)

    reader.onload = e => {

        container.innerHTML = '<img class="img_item" src = "">'

        container.querySelector('img').setAttribute('src', e.target.result)
    }

}