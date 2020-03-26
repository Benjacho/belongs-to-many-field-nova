Nova.booting((Vue, router, store) => {
  Vue.directive('select-overflow', {
    inserted: (el, _binding, vnode) => {
      let originalWidth
      let originalPosition
      let originalZIndex
      let selectIsOpen = false
      vnode.child.$watch('isOpen', isOpen => {
        selectIsOpen = isOpen
        if (isOpen) {
          const {offsetWidth} = el
          originalWidth = el.style.width
          originalPosition = el.style.position
          originalZIndex = el.style.zIndex
          el.style.width = `${offsetWidth}px`
          el.style.position = 'fixed'
          el.style.zIndex = 2
        } else {
          el.style.position = originalPosition
          el.style.width = originalWidth
          el.style.zIndex = originalZIndex
        }
      })

      window.addEventListener('wheel', event => {
        if (selectIsOpen) {
          // disabled outside scroll when select is open
          event.stopPropagation()
        }
      }, true)
    },
  });


  Vue.component('index-BelongsToManyField', require('./components/IndexField'))
  Vue.component('detail-BelongsToManyField', require('./components/DetailField'))
  Vue.component('form-BelongsToManyField', require('./components/FormField'))
})
