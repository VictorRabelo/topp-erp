import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatGridListModule } from '@angular/material/grid-list';
import { MatInputModule } from '@angular/material/input';
import { MatTableModule } from '@angular/material/table';
import { MatStepperModule } from '@angular/material/stepper';
import { MatIconModule } from '@angular/material/icon';

import { LOCALE_ID, NgModule } from '@angular/core';

import { HttpClientModule } from '@angular/common/http';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap'

import { HotkeyModule } from 'angular2-hotkeys';

import { AppComponent } from './app.component';

import { registerLocaleData } from '@angular/common';
import localeBr from '@angular/common/locales/pt';
import localeBrExtra from '@angular/common/locales/extra/pt';
import { ModalLoginComponent } from './shared/modal-login/modal-login.component';
import { ToastrModule } from 'ngx-toastr';
import { ModalProductsComponent } from './shared/modal-products/modal-products.component';
import { ModalSettingsComponent } from './shared/modal-settings/modal-settings.component';

import { PrintService, ThermalPrintModule } from 'ng-thermal-print';
import { ModalVendasComponent } from './shared/modal-vendas/modal-vendas.component';
import { ModalCheckoutComponent } from './shared/modal-checkout/modal-checkout.component'; //add this line
import { CurrencyMaskInputMode, NgxCurrencyModule } from 'ngx-currency';
import { ModalDescontoComponent } from './shared/modal-desconto/modal-desconto.component';
import { ModalParcelasComponent } from './shared/modal-parcelas/modal-parcelas.component';



export const customCurrencyMaskConfig = {
    align: "right",
    allowNegative: true,
    allowZero: true,
    decimal: ",",
    precision: 2,
    prefix: "R$ ",
    suffix: "",
    thousands: ".",
    nullable: true,
    min: null,
    max: null,
    inputMode: CurrencyMaskInputMode.FINANCIAL
};


registerLocaleData(localeBr, 'br', localeBrExtra);

@NgModule({
    declarations: [
        AppComponent,
        ModalLoginComponent,
        ModalProductsComponent,
        ModalSettingsComponent,
        ModalVendasComponent,
        ModalCheckoutComponent,
        ModalDescontoComponent,
        ModalParcelasComponent
    ],
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        FormsModule,
        HttpClientModule,

        HotkeyModule.forRoot(),
        ToastrModule.forRoot(),
        ThermalPrintModule,

        NgxCurrencyModule.forRoot(customCurrencyMaskConfig),

        NgbModule,

        MatTableModule,
        MatCardModule,
        MatFormFieldModule,
        MatInputModule,
        MatGridListModule,
        MatButtonModule,
        MatStepperModule,
        MatIconModule
    ],
    entryComponents: [
        ModalLoginComponent,
        ModalProductsComponent,
        ModalSettingsComponent,
        ModalVendasComponent,
        ModalCheckoutComponent,
        ModalDescontoComponent,
        ModalParcelasComponent
    ],
    providers: [
        { provide: LOCALE_ID, useValue: 'br' },
        PrintService,
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
