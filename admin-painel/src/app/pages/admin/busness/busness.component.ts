import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ModelService } from '../../../services/model.service';
import { MessageService } from '../../../services/message.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { BusnessFormComponent } from './busness-form/busness-form.component';
import { Router } from '@angular/router';

@Component({
  selector: 'app-busness',
  templateUrl: './busness.component.html',
  styleUrls: ['./busness.component.scss']
})
export class BusnessComponent implements OnInit {

  dataSource = [];

  constructor(
    private service: ModelService,
    private message: MessageService,
    private ref: ChangeDetectorRef,
    private modalCtrl: NgbModal,
    private route: Router
  ) { }

  ngOnInit(): void {
    this.loadList();
  }

  loadList() {
    this.message.loading(true);
    this.service.getEmpresas({}).subscribe((res: any) => {
      console.log(res);
      this.dataSource = res;
      this.message.loading();
    }, (erro) => {
      console.log(erro);
      this.message.loading();
    })
  }

  open(item) {
    const modalRef = this.modalCtrl.open(BusnessFormComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.result.then(res => {
      if (res) {
        this.loadList();
      }
    });
  }

  delete(item) {
    this.message.swal.fire({
      title: 'Deletar Empresa?',
      html: `Deseja realmente deletar a empresa: <br> <b>${item.razao}</b> ?`,
      icon: 'warning',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Sim!',
      showCancelButton: true,
      cancelButtonColor: '#d33',
      cancelButtonText: 'Não'
    }).then((result) => {

      if (result.value) {
        this.message.loading(true);
        this.service.deleteEmpresa(item.id).subscribe((res: any) => {
          this.message.loading();
          this.loadList();
        }, (erro) => {
          console.log(erro);
          this.message.loading();
        });
      }

    });
  }

  open_users(item) {
    this.route.navigate(['/admin/users/' + item.id]);
  }

}
