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
import { MatButtonModule, MatTooltipModule, MatTabsModule, MatIconModule } from '@angular/material';

import { ClientesComponent } from './clientes/clientes.component';
import { ClienteFormComponent } from './clientes/cliente-form/cliente-form.component';
import { ProdutosComponent } from './produtos/produtos.component';
import { ProdutoFormComponent } from './produtos/produto-form/produto-form.component';


const routes: Routes = [
   {
      path: 'clientes', children: [
         { path: '', component: ClientesComponent }
      ]
   },
   { path: 'produtos', component: ProdutosComponent }
]

@NgModule({
   declarations: [
      ClientesComponent,
      ClienteFormComponent,
      ProdutosComponent,
      ProdutoFormComponent,
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

      MatButtonModule,
      MatTooltipModule,
      MatTabsModule,
      MatIconModule
   ],
   entryComponents: [
      ClienteFormComponent
   ],
   providers: []
})
export class PagesModule {
}
