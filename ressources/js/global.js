document.querySelector('.label_addtel__')?.addEventListener('click', ()=>{
    let element = document.querySelector('.tel__').cloneNode(true);

    element.querySelector('.tel_numero').value = '';
    element.querySelector('.tel_poste').value = '';
    element.querySelector('.tel_type').value = '';

    document.querySelector('.label_addtel__').insertAdjacentElement('beforebegin', element);

})