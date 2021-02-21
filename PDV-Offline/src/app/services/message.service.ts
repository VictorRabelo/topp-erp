import { Injectable } from "@angular/core";

// ES6 Modules or TypeScript
import Swal from 'sweetalert2';
import { ToastrService } from 'ngx-toastr';

@Injectable({
   providedIn: 'root'
})
export class MessageService {
   public swal = Swal;

   constructor(
      public toast: ToastrService
   ) { }

   alertErro(msg = '', titulo = 'Ops!') {
      Swal.fire({
         icon: 'error',
         title: titulo,
         html: msg,
      });
   }
   alertSuccess(msg = "") {
      Swal.fire({
         icon: 'success',
         // title: 'Falha na conexão',
         html: msg,
         allowOutsideClick: true,
      }).then(resp => { });
   }
   alertNet() {
      Swal.fire({
         icon: 'error',
         title: 'Falha na conexão',
         html: 'Parece que você está sem internet, verifique a conexão!',
         allowOutsideClick: false,
      }).then(resp => {
         if (resp.value) {
            location.reload();
         }
      });
   }
   alertTroco(troco) {
      Swal.fire({
         icon: 'success',
         title: 'Venda Finalizada.',
         html: `Troco: R$ <b>${troco}</b>`,
         // allowOutsideClick: true,
      }).then(resp => { });
   }

   public toastError(msg = '', title = '') {
      this.toast.error(msg, title);
   }
   public toastSuccess(msg = '', title = '') {
      this.toast.success(msg, title);
   }


   loading(type = false, texto = "") {
      const _loading = document.getElementById('loading');
      if (type == false) {
         _loading.style.display = 'none';
         _loading.innerHTML = '';
      } else {
         _loading.style.display = 'flex';
         _loading.innerHTML = ' <div class="loading-body loading-md"><div class="lds-dual-ring"></div><span>' + texto + '</span></div>';
      }
   }
}