import { Injectable } from '@angular/core';
import { Connection, ConnectionOptions, createConnection } from 'typeorm';
import { Settings } from './repositories/settings';
import { User } from './entities/user.entity';
import { Product } from './entities/product.entity';
import { Empresa } from './entities/empresa.entity';
import { UserPermission } from './entities/user_permission.entity';
import { Venda } from './entities/venda.entity';
import { VendaItens } from './entities/venda_itens.entity';
import { VendaPayments } from './entities/venda_payments.entity';
import { PaymentsForm } from './entities/payments_form.entity';

@Injectable({
    providedIn: 'root'
})
export class DatabaseService {

    public connection: Promise<Connection>;
    private readonly options: ConnectionOptions;

    constructor() {
        Settings.initialize();
        this.options = {
            type: 'sqlite',
            database: Settings.dbPath,
            entities: [Empresa, User, UserPermission, PaymentsForm, Venda, VendaItens, VendaPayments, Product],
            synchronize: true,
            logging: 'all',
        };
        this.connection = createConnection(this.options);
    }
}
