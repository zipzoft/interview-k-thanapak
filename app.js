// const app = new Vue({
//     el: '#app',
//     data: {
//     message: 'Ahoy Vue!',
//     cart: 0,
//     Name: 'Spiderman',
//     Price: 950,
//     Descrip: 'Spiderman ภาคใหม่ไฉไลกว่าเดิม'
//     }
// })
var app = new Vue({
    el: '#app',
    data: {
      title: 'Hello Vue!',
      name: 'thip'
    },
    methods: {
        sayGreeting : function () {
          return this.title + ' : ' + this.name
        }
    } 
  })