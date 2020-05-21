// Angular
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
// Partials
import { PartialsModule } from '../partials/partials.module';

// Pages
import { CoreModule } from '../../core/core.module';
import { RouterModule, Routes } from '@angular/router';

import { NgxMaskModule } from 'ngx-mask';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {
   MatButtonModule, MatTooltipModule,
   MatTabsModule, MatIconModule,
   MatProgressSpinnerModule
} from '@angular/material';
import { NgxCurrencyModule, CurrencyMaskInputMode } from "ngx-currency";

import { DebounceModule } from 'ngx-debounce';

import { ClientesComponent } from './clientes/clientes.component';
import { ClienteFormComponent } from './clientes/cliente-form/cliente-form.component';
import { ProdutosComponent } from './produtos/produtos.component';
import { ProdutoFormComponent } from './produtos/produto-form/produto-form.component';
import { VendaBalcaoComponent } from './vendas/venda-balcao/venda-balcao.component';
import { VendasComponent } from './vendas/vendas.component';

export const customCurrencyMaskConfig = {
   align: "right",
   allowNegative: true,
   allowZero: true,
   decimal: ",",
   precision: 2,
   prefix: "R$ ",
   suffix: "",
   thousands: ".",
   nullable: false,
   min: null,
   max: null,
   inputMode: CurrencyMaskInputMode.FINANCIAL
};

const routes: Routes = [
   {
      path: 'clientes', children: [
         { path: '', component: ClientesComponent }
      ]
   },
   { path: 'produtos', component: ProdutosComponent }
];


@NgModule({
   declarations: [
      ClientesComponent,
      ClienteFormComponent,
      ProdutosComponent,
      ProdutoFormComponent,
      VendaBalcaoComponent,
      VendasComponent,
   ],
   exports: [],
   imports: [
      CommonModule,
      HttpClientModule,
      FormsModule,
      ReactiveFormsModule,
      CoreModule,
      PartialsModule,
      RouterModule.forChild(routes),
      NgbModule,
      NgxMaskModule.forRoot(),
      NgxCurrencyModule.forRoot(customCurrencyMaskConfig),
      DebounceModule,

      MatButtonModule,
      MatTooltipModule,
      MatTabsModule,
      MatIconModule,
      MatProgressSpinnerModule
   ],
   entryComponents: [
      ClienteFormComponent,
      ProdutoFormComponent
   ],
   providers: []
})
export class PagesModule {
}
