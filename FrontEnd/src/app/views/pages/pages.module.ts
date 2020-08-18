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
import { ProdutosComponent } from './produtos/produtos.component';
import { VendaBalcaoComponent } from './vendas/venda-balcao/venda-balcao.component';
import { VendasComponent } from './vendas/vendas.component';
import { UsersComponent } from './users/users.component';
import { EmitentesComponent } from './emitentes/emitentes.component';
import { EmitenteDetalheComponent } from './emitentes/emitente-detalhe/emitente-detalhe.component';
import { NfceComponent } from './nfce/nfce.component';
import { NfeComponent } from './nfe/nfe.component';
import { NfeFormComponent } from './nfe/nfe-form/nfe-form.component';
import { SharedModule } from '../../shared/shared.module';
import { FormsPaymentsComponent } from './financeiro/forms-payments/forms-payments.component';
import { PaymentFormComponent } from './financeiro/forms-payments/payment-form/payment-form.component';
import { CaixaResumoComponent } from './financeiro/caixa-resumo/caixa-resumo.component';
import { ContasReceberComponent } from './contas-receber/contas-receber.component';
import { ContasPagarComponent } from './contas-pagar/contas-pagar.component';
import { ContaPagarFormComponent } from './contas-pagar/conta-pagar-form/conta-pagar-form.component';
import { ContaPagarPaymentComponent } from './contas-pagar/conta-pagar-payment/conta-pagar-payment.component';
import { ContaReceberFormComponent } from './contas-receber/conta-receber-form/conta-receber-form.component';
import { ContaReceberPaymentComponent } from './contas-receber/conta-receber-payment/conta-receber-payment.component';

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
	{ path: 'nfe', component: NfeComponent },
	{ path: 'nfce', component: NfceComponent },
	{ path: 'caixa', component: CaixaResumoComponent },
	{ path: 'caixa/payments_forms', component: FormsPaymentsComponent },
	{ path: 'contas_pagar', component: ContasPagarComponent },
	{ path: 'contas_receber', component: ContasReceberComponent },

	{ path: 'nfe/:id', component: NfeFormComponent },
	{ path: 'emitente/:id', component: EmitenteDetalheComponent },
	{ path: 'venda_standart/:id', component: VendaBalcaoComponent }
];


@NgModule({
	declarations: [
		ClientesComponent,
		// ClienteFormComponent,
		ProdutosComponent,
		// ProdutoFormComponent,
		VendaBalcaoComponent,
		VendasComponent,
		UsersComponent,
		EmitentesComponent,
		EmitenteDetalheComponent,
		NfceComponent,
		NfeComponent,
		NfeFormComponent,
		CaixaResumoComponent,
		FormsPaymentsComponent,
		PaymentFormComponent,
		ContasReceberComponent,
		ContasPagarComponent,
		ContaPagarFormComponent,
		ContaPagarPaymentComponent,
		ContaReceberFormComponent,
		ContaReceberPaymentComponent,
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
		SharedModule,

		MatButtonModule,
		MatTooltipModule,
		MatTabsModule,
		MatIconModule,
		MatProgressSpinnerModule,
		MatCheckboxModule
	],
	entryComponents: [
		PaymentFormComponent,
		ContaPagarFormComponent,
		ContaPagarPaymentComponent,
		ContaReceberFormComponent,
		ContaReceberPaymentComponent
	],
	providers: []
})
export class PagesModule {
}
