import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { DatabaseService } from '../../tools/database.service';
import AuthService from '../../services/auth.service';

import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { Empresa } from '../../tools/entities/empresa.entity';
import { User } from '../../tools/entities/user.entity';
import { MessageService } from '../../services/message.service';
import { UserPermission } from 'src/app/tools/entities/user_permission.entity';
import { Product } from 'src/app/tools/entities/product.entity';
import { ToolsService } from '../../services/tools.service';

@Component({
  selector: 'app-modal-login',
  templateUrl: './modal-login.component.html',
  styleUrls: ['./modal-login.component.scss']
})
export class ModalLoginComponent implements OnInit {

  @ViewChild('inputEmail', { static: true }) inputEmail: ElementRef;

  constructor(
    private activeModal: NgbActiveModal,
    private service: AuthService,
    private database: DatabaseService,
    private tools: ToolsService,
    private message: MessageService
  ) { }

  ngOnInit() {
    // this.database.connection.then(() => );
    this.inputEmail.nativeElement.focus();
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  submit(form) {
    console.log(form);
    if (!form.valid) {

      return false;
    }

    this.message.loading(true);
    this.verificaDB(form.value);
  }

  async verificaDB(dados) {
    console.log('dados', dados);

    const user = await User.findOne({ where: { email: dados.email, password: dados.password } });
    console.log('user', user);

    if (user) {
      this.message.loading()
      this.close(user);

    } else { //verifica se tem na nuvem

      this.service.loginServe(dados).then(async (res) => {
        console.log(res);
        const empresa = await Empresa.findOne({ where: { empresa_id: res.empresa_id } });
        if (!empresa) {
          this.alertNewBusiness(res);
        } else {
          let newUser = res.user;
          newUser.password = dados.password;
          console.log('newUser', newUser);
          await User.insert(newUser);
          await UserPermission.insert(res.permissions);

          this.close(newUser)
        }
        console.log('empresa', empresa);
      }).catch(err => {
        console.log('err login serve', err)
        if (err.error.message) {
          this.message.alertErro(err.error.message);
        }
      }).finally(() => this.message.loading());
    }
  }

  alertNewBusiness(dados) {
    this.message.swal.fire({
      icon: 'warning',
      title: 'Nova empresa identificada!',
      html: 'Iniciar uma carga das informações do servidor ?',
      confirmButtonText: 'Confirmar',
      showCancelButton: true,
      cancelButtonText: 'Não',
      allowOutsideClick: false,
    }).then(async (resp) => {
      if (resp.isConfirmed) {

        const newBusiness = await Empresa.insert(dados.empresa);
        console.log('newBusiness', newBusiness);

        await this.tools.startCarga(dados.empresa);

        this.close(dados.user);
      }
    });
  }

}
