import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { ModelService } from '../../../services/model.service';
import { MessageService } from '../../../services/message.service';
import { UsuarioFormComponent } from './usuario-form/usuario-form.component';


@Component({
  selector: 'app-usuarios',
  templateUrl: './usuarios.component.html',
  styleUrls: ['./usuarios.component.scss']
})
export class UsuariosComponent implements OnInit {

  dataSource = [];
  empresa_id: number;

  constructor(
    private service: ModelService,
    private message: MessageService,
    private ref: ChangeDetectorRef,
    private modalCtrl: NgbModal,
    private route: Router,
    private rota: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.rota.params.subscribe((params: any) => {
      console.log(params);
      if (params) {
        this.empresa_id = params.id;
        this.loadList();
      }
    })
  }

  close() {
    this.route.navigate(['/admin/busness']);
  }

  loadList() {
    this.message.loading(true);
    this.service.getUsers({ empresa_id: this.empresa_id }).subscribe((res: any) => {
      console.log(res);
      this.dataSource = res;
      this.message.loading();
    }, (erro) => {
      console.log(erro);
      this.message.loading();
    })
  }

  open(item) {
    const modalRef = this.modalCtrl.open(UsuarioFormComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = { 'empresa_id': this.empresa_id, 'dados': item };
    modalRef.result.then(res => {
      if (res) {
        this.loadList();
      }
    });
  }

  delete(item) {
    this.message.swal.fire({
      title: 'Deletar Usuário?',
      html: `Deseja realmente deletar o usuário: <br> <b>${item.nome}</b> ?`,
      icon: 'warning',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Sim!',
      showCancelButton: true,
      cancelButtonColor: '#d33',
      cancelButtonText: 'Não'
    }).then((result) => {

      if (result.value) {
        this.message.loading(true);
        this.service.deleteUser(item.id).subscribe((res: any) => {
          this.message.loading();
          this.loadList();
        }, (erro) => {
          console.log(erro);
          this.message.loading();
        });
      }

    });
  }

}
