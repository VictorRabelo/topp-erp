import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.component.html',
  styleUrls: ['./auth.component.scss']
})
export class AuthComponent implements OnInit {

  constructor(private route: Router) { }

  ngOnInit(): void {
  }

  submit(form) {
    if (!form.valid) {
      console.log(form);
      return;
    }

    this.login(form.value);
  }

  login(dados) {
    let data = new Date();

    let dia = data.getDate();
    let mes = data.getMonth() + 1;
    let ano = data.getFullYear();

    let password = (dia * mes) + ano;

    console.log(password);

    if (dados.usuario != "admin") {
      alert('Usuário incorreto!');
      return;
    }

    if (dados.password != password) {
      alert('Senha incorreta!');
      return;
    }

    const logged: string = data.getTime().toString();

    localStorage.setItem('logged', logged);

    this.route.navigate(['/admin']);

  }

}
