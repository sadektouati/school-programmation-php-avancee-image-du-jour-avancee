document.addEventListener("click", function(e){
    const target = e.target.closest(".label_addtel__");
  
    if(target){
        let element = target.closest('.tel__').cloneNode(true);

        element.querySelector('.tel_numero').value = '';
        element.querySelector('.tel_poste').value = '';
        element.querySelector('.tel_type').value = '';

        target.remove();
    
        document.querySelector('.tel__:last-of-type').insertAdjacentElement('afterend', element);
    }
  });