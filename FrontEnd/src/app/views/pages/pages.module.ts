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
   MatProgressSpinnerModule,
   MatCheckboxModule
} from '@angular/material';
import { NgxCurrencyModule, CurrencyMaskInputMode } from "ngx-currency";

import { DebounceModule } from 'ngx-debounce';

import { ClientesComponent } from './clientes/clientes.component';
import { ClienteFormComponent } from './clientes/cliente-form/cliente-form.component';
import { ProdutosComponent } from './produtos/produtos.component';
import { ProdutoFormComponent } from './produtos/produto-form/produto-form.component';
import { VendaBalcaoComponent } from './vendas/venda-balcao/venda-balcao.component';
import { VendasComponent } from './vendas/vendas.component';
import { UsersComponent } from './users/users.component';
import { UserFormComponent } from './users/user-form/user-form.component';
import { PermissionsFormComponent } from './users/permissions-form/permissions-form.component';
import { ProdutoSearchComponent } from './vendas/modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from './vendas/modais/produto-detalhe/produto-detalhe.component';
import { ClienteSearchComponent } from './vendas/modais/cliente-search/cliente-search.component';
import { ClienteManualComponent } from './vendas/modais/cliente-manual/cliente-manual.component';
import { VendaFinishComponent } from './vendas/modais/venda-finish/venda-finish.component';
import { GeraParcelasComponent } from './vendas/modais/gera-parcelas/gera-parcelas.component';
import { EmitentesComponent } from './emitentes/emitentes.component';
import { EmitenteFormComponent } from './emitentes/emitente-form/emitente-form.component';
import { EmitenteConfigFormComponent } from './emitentes/emitente-config-form/emitente-config-form.component';
import { EmitenteDetalheComponent } from './emitentes/emitente-detalhe/emitente-detalhe.component';
import { EmitenteCertificateComponent } from './emitentes/emitente-certificate/emitente-certificate.component';

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
   { path: 'users', component: UsersComponent },
   { path: 'produtos', component: ProdutosComponent },
   { path: 'emitentes', component: EmitentesComponent },
   { path: 'vendas', component: VendasComponent },
   { path: 'emitente/:id', component: EmitenteDetalheComponent },
   { path: 'venda_standart/:id', component: VendaBalcaoComponent }
];


@NgModule({
   declarations: [
      ClientesComponent,
      ClienteFormComponent,
      ProdutosComponent,
      ProdutoFormComponent,
      VendaBalcaoComponent,
      VendasComponent,
      UsersComponent,
      UserFormComponent,
      PermissionsFormComponent,
      ProdutoSearchComponent,
      ProdutoDetalheComponent,
      ClienteSearchComponent,
      ClienteManualComponent,
      VendaFinishComponent,
      GeraParcelasComponent,
      EmitentesComponent,
      EmitenteFormComponent,
      EmitenteConfigFormComponent,
      EmitenteDetalheComponent,
      EmitenteCertificateComponent,
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
      MatProgressSpinnerModule,
      MatCheckboxModule
   ],
   entryComponents: [
      ClienteFormComponent,
      ProdutoFormComponent,
      PermissionsFormComponent,
      UserFormComponent,
      ProdutoSearchComponent,
      ProdutoDetalheComponent,
      ClienteSearchComponent,
      VendaFinishComponent,
      GeraParcelasComponent,
      EmitenteFormComponent,
      EmitenteConfigFormComponent,
      EmitenteCertificateComponent
   ],
   providers: []
})
export class PagesModule {
}
