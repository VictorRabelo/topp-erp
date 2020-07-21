import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClienteFormComponent } from '../views/pages/clientes/cliente-form/cliente-form.component';
import { ProdutoFormComponent } from '../views/pages/produtos/produto-form/produto-form.component';
import { PermissionsFormComponent } from '../views/pages/users/permissions-form/permissions-form.component';
import { UserFormComponent } from '../views/pages/users/user-form/user-form.component';
import { ProdutoSearchComponent } from '../views/pages/modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from '../views/pages/modais/produto-detalhe/produto-detalhe.component';
import { ClienteSearchComponent } from '../views/pages/modais/cliente-search/cliente-search.component';
import { VendaFinishComponent } from '../views/pages/modais/venda-finish/venda-finish.component';
import { GeraParcelasComponent } from '../views/pages/modais/gera-parcelas/gera-parcelas.component';
import { EmitenteFormComponent } from '../views/pages/emitentes/emitente-form/emitente-form.component';
import { EmitenteConfigFormComponent } from '../views/pages/emitentes/emitente-config-form/emitente-config-form.component';
import { EmitenteCertificateComponent } from '../views/pages/emitentes/emitente-certificate/emitente-certificate.component';
import { GeraNotaComponent } from '../views/pages/modais/gera-nota/gera-nota.component';
// import { NfeFormComponent } from '../views/pages/nfe/nfe-form/nfe-form.component';
import { ProdutoMovStockComponent } from '../views/pages/produtos/produto-mov-stock/produto-mov-stock.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CurrencyMaskInputMode, NgxCurrencyModule } from 'ngx-currency';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { NgxMaskModule } from 'ngx-mask';
import { PartialsModule } from '../views/partials/partials.module';
import { CoreModule } from '../core/core.module';
import { DebounceModule } from 'ngx-debounce';
import { MatButtonModule, MatTooltipModule, MatTabsModule, MatIconModule, MatProgressSpinnerModule, MatCheckboxModule } from '@angular/material';
import { FiscalSendXmlComponent } from '../views/pages/modais/fiscal-send-xml/fiscal-send-xml.component';

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

@NgModule({
	declarations: [
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
		EmitenteCertificateComponent,
		GeraNotaComponent,
		// NfeFormComponent,
		ProdutoMovStockComponent,
		FiscalSendXmlComponent
	],
	imports: [
		CommonModule,
		HttpClientModule,
		FormsModule,
		ReactiveFormsModule,
		CoreModule,
		PartialsModule,
		// RouterModule.forChild(routes),
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
		EmitenteCertificateComponent,
		GeraNotaComponent,
		// NfeFormComponent,
		ProdutoMovStockComponent,
		FiscalSendXmlComponent
	]
})
export class SharedModule { }
