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

                container = parentContainer.querySelector('.empty_container')
                console.log(container)

                if (container.length < this.files.length) {

                    for (let index = 0; index < this.files.length - container.length; index++) {

                        let el = document.createElement('div')

                        el.classList.add('vg-dotted-square', 'g-center', 'empty_container')

                        parentContainer.append(el)
                    }
                }

            }
        }
    })
console.log('end')
}