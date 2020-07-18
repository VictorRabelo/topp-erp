import { Component, OnInit, Input } from '@angular/core';
import { MessageService } from '../../../../services/message.service';
import { ProductService } from '../../../../services/product.service';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ProdutoMovStockComponent } from '../produto-mov-stock/produto-mov-stock.component';

@Component({
   selector: 'kt-produto-form',
   templateUrl: './produto-form.component.html',
   styleUrls: ['./produto-form.component.scss']
})
export class ProdutoFormComponent implements OnInit {

   dados: any = {};

   submited = false;
   loadingCep = false;

   // files: any;

   @Input() public data;

   constructor(
      private message: MessageService,
      private service: ProductService,
      private activeModal: NgbActiveModal,
      private modalCtrl: NgbModal
   ) { }

   ngOnInit() {
      if (this.data != undefined) {
         this.dados = this.data;
         this.getDados();
      }

   }

   close(params = undefined) {
      this.activeModal.close(params);
   }

   getTitle() {
      if (this.dados.id > 0) {
         return "Alterar Cadastro";
      } else {
         return "Novo Cadastro";
      }

   }

   getDados() {
      this.service.getById(this.dados.id).subscribe(resp => {
         this.dados = resp;
      }, erro => {
         this.close();
      })
   }

   onSubmit(form) {

      if (!form.valid) {
         return;
      }

      if (this.dados.id > 0) {
         this.update();
      } else {
         this.create();
      }

   }

   create() {
      this.submited = true;
      this.service.create(this.dados).subscribe(resp => {
         this.submited = false;
         // console.log(resp);
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

   update() {
      this.submited = true;
      this.service.update(this.dados, this.dados.id).subscribe(resp => {
         this.submited = false;
         // console.log(resp);
         this.close(true);
      }, (erro) => {
         this.submited = false;
      });
   }

   onFileSelected(event) {
      const selectedFiles = <FileList>event.srcElement.files;
      const files = [];
      const names = [];
      for (let i = 0; i < selectedFiles.length; i++) {
         names.push(selectedFiles[i].name);
         this.getBase64(selectedFiles[i]).then(
            (data: string) => files.push(btoa(data))
         );
      }
      this.dados.foto = files;
      this.dados.photo_name = names[0];
   }
   getBase64(file) {
      return new Promise((resolve, reject) => {
         const reader = new FileReader();
         reader.readAsBinaryString(file);
         reader.onload = () => resolve(reader.result);
         reader.onerror = error => reject(error);
      });
   }

   calc_margem() {
      const custo = (this.dados.custo) ? this.dados.custo : 0;
      let margem = 0;
      let preco = (this.dados.preco) ? this.dados.preco : 0;

      if (custo > 0 && preco > 0) {
         margem = (preco - custo) / custo;
         margem = margem * 100;
      }

      this.dados.custo = custo;
      this.dados.margem = margem;
      this.dados.preco = preco;

   }
   calc_preco() {
      const custo = (this.dados.custo) ? this.dados.custo : 0;
      let margem = (this.dados.margem) ? this.dados.margem : 0;
      let preco = 0;

      if (custo > 0 && margem > 0) {
         preco = (margem / 100);
         preco = (custo * preco) + custo;
      }

      this.dados.custo = custo;
      this.dados.margem = margem;
      this.dados.preco = preco;
   }


   openMovEstoque() {
      const modalRef = this.modalCtrl.open(ProdutoMovStockComponent, {size: 'md', backdrop: 'static'});
      modalRef.componentInstance.data = this.dados;
      // modalRef.result.then(resp => {
         
      // })
   }

}
