import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from 'src/app/services/message.service';
import { Like } from 'typeorm';
import { Venda } from '../../tools/entities/venda.entity';

@Component({
  selector: 'app-modal-vendas',
  templateUrl: './modal-vendas.component.html',
  styleUrls: ['./modal-vendas.component.scss']
})
export class ModalVendasComponent implements OnInit {

  screen = window.innerHeight;

  filters: any = { status: 'todos', created_at: new Date().toISOString().substr(0, 10) };

  dataSource = [];

  @Input() data: any;

  constructor(
    private activeModal: NgbActiveModal,
    private message: MessageService
  ) { }

  ngOnInit() {
    this.filters.empresa_id = this.data.id;
    this.getList();
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  async getList() {
    const query: any = { empresa_id: this.filters.empresa_id, created_at: Like(`%${this.filters.created_at}%`) };

    if (this.filters.status !== "todos") {
      query.status = this.filters.status
      // console.log('status', this.filters.status);
    }

    this.dataSource = await Venda.find({
      where: [
        query
      ], order: { created_at: 'DESC' }
    });
  }

  open_item(item) {

    if (item.status == 10 || item.status == 0) {
      return false;
    }

    this.close(item.id);
  }

}
