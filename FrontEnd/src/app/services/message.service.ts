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

   public toastError(msg = '', title = '') {
      this.toast.error(msg, title);
   }
   public toastSuccess(msg = '', title = '') {
      this.toast.success(msg, title);
   }
}