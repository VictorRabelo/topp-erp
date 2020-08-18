import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AdminComponent } from './admin.component';
import { HomeComponent } from './home/home.component';
import { BusnessComponent } from './busness/busness.component';
import { Routes, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { BusnessFormComponent } from './busness/busness-form/busness-form.component';

import { NgxMaskModule, IConfig } from 'ngx-mask';
import { UsuariosComponent } from './usuarios/usuarios.component';
import { UsuarioFormComponent } from './usuarios/usuario-form/usuario-form.component';


const routes: Routes = [
  {
    path: '',
    component: AdminComponent,
    children: [

      { path: '', redirectTo: 'home', pathMatch: 'full' },

      { path: 'home', component: HomeComponent },
      { path: 'busness', component: BusnessComponent },
      { path: 'users/:id', component: UsuariosComponent }
    ]
  }
]

@NgModule({
  declarations: [AdminComponent, HomeComponent, BusnessComponent, BusnessFormComponent, UsuariosComponent, UsuarioFormComponent],
  imports: [
    CommonModule,
    RouterModule.forChild(routes),
    FormsModule,

    NgxMaskModule.forChild(),
  ],
  entryComponents: [
    BusnessFormComponent
  ]
})
export class AdminModule { }
