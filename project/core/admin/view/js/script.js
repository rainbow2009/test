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

createFile();

function createFile() {

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

                let attributeName = fileName.replace(/[\[\]]/g, '')

                for (let i in this.files) {

                    if (this.files.hasOwnProperty(i)) {

                        if (multiple) {
                            if (typeof fileStore[fileName] === 'undefined') fileStore[fileName] = []

                            if (typeof fileStore[fileName] === 'undefined') fileStore[fileName] = []

                            let elId = fileStore[fileName].push(this.files[i]) - 1

                            container[i].setAttribute(`data-deleteFileId-${attributeName}`, elId)

                            showImage(this.files[i], container[i])

                            deleteNewFiles(elId, fileName, attributeName, container[i])


                        } else {

                            container = this.closest('.img_container').querySelector('.img_show')
                            showImage(this.files[i], container)

                        }

                    }
                }
            }
        })
    }

    let form = document.querySelector('#main-form')

    if (form) {

        form.onsubmit = function (e) {


            if (!isEmpty(fileStore)) {

                e.preventDefault()

                let forData = new FormData(this)

                for (let i in fileStore) {

                    if (fileStore.hasOwnProperty(i)) {

                        forData.delete(i)

                        let rowName = i.replace(/[\[\]]/g, '')

                        fileStore[i].forEach((item, index) => {

                            forData.append(`${rowName}[${index}]`, item)

                        })

                    }
                }


                forData.append('ajax', 'editData')

                Ajax({
                    url: this.getAttribute('action'),
                    type: 'post',
                    data: forData,
                    processData: false,
                    contentType: false
                }).then(res => {

                    try {

                        res = JSON.parse(res)

                        if (!res.success) {
                            throw new Error()
                        }

                        location.reload()

                    } catch (e) {
                        alert('произошла ошибка')
                    }


                })

            }

        }
    }

    function showImage(item, container) {

        let reader = new FileReader()

        container.innerHTML = '';

        reader.readAsDataURL(item)

        reader.onload = e => {

            container.innerHTML = '<img class="img_item" src = "">'

            container.querySelector('img').setAttribute('src', e.target.result)

            container.classList.remove('empty_container')
        }

    }


    function deleteNewFiles(elId, fileName, attributeName, container) {

        container.addEventListener('click', function () {

            this.remove()

            delete fileStore[fileName][elId]
        })


    }
}

changeMenuPosition()

function changeMenuPosition() {

    let form = document.querySelector('#main-form')

    if (form) {
        let selectParent = form.querySelector('select[name=parent_id]')
        let selectPosition = form.querySelector('select[name=menu_position]')

        if (selectPosition && selectParent) {

            let defaultParent = selectParent.value

            let defaultPosition = +selectPosition.value

            selectParent.addEventListener('change', function () {

                let defaultChose = false


                if (this.value === defaultParent) {
                    defaultChose = true
                }
                Ajax({
                    data: {
                        table: form.querySelector('input[name=table]').value,
                        'parent_id': this.value,
                        ajax: 'change_parent',
                        iteration: !form.querySelector('#table_id') ? 1 : +!defaultChose
                    }
                }).then(res => {


                    res = +res


                    if (!res) return errorAlert()

                    let newSelect = document.createElement('select')

                    newSelect.setAttribute('name', 'menu_position')

                    newSelect.classList.add('vg-input', 'vg-text', 'vg-full', 'vg-firm-color1')

                    for (let i = 1; i <= res; i++) {

                        let selected = defaultChose && i === defaultChose ? 'selected' : ''

                        newSelect.insertAdjacentHTML('beforeend', `<option ${selected} value="${i}">${i}</option>`)
                    }

                    selectPosition.before(newSelect)

                    selectPosition.remove()

                    selectPosition = newSelect

                })

            })

        }

    }

}

blockParameters()

function blockParameters() {

    let wraps = document.querySelectorAll('.select_wrap')


    if (wraps.length) {

        let selectAllIndexes = []

        wraps.forEach(item => {

            let next = item.nextElementSibling

            if (next && next.classList.contains('option_wrap')) {

                item.addEventListener('click', e => {

                    if (!e.target.classList.contains('select_all')) {

                       next.slideToggle()

                    } else {

                        let index =[... document.querySelectorAll('.select_all')].indexOf(e.target)

                        if(typeof selectAllIndexes[index] === 'undefined') selectAllIndexes[index] = false

                        selectAllIndexes[index] = !selectAllIndexes[index]

                        next.querySelectorAll("input[type=checkbox]").forEach(el =>{
                            el.checked = selectAllIndexes[index]
                        })

                    }

                })


            }
        })

    }

}



