import { Injectable } from "@angular/core";
import { ToastrService } from 'ngx-toastr';
import Swal from 'sweetalert2'

@Injectable({
  providedIn: 'root'
})
export class MessageService {

  public swal = Swal;

  constructor(
    private toastr: ToastrService
  ) { }

  loading(type = false) {

    let load = document.getElementById('loading');

    if (type == true) {
      load.style.display = 'block';
    } else {
      load.style.display = 'none';
    }
  }

  showSuccess(msg, title = "Sucesso!") {
    this.toastr.success(msg, title);
  }

  showError(msg, title = "Falha!") {
    this.toastr.error(msg, title);
  }

  alertError(msg, title = "Ops!") {
    Swal.fire({
      title: title,
      text: msg,
      icon: 'error',
      confirmButtonText: 'Ok'
    });
  }
}
