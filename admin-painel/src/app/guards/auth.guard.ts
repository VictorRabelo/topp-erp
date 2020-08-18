import { Injectable } from "@angular/core";
import { CanActivate, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(private route: Router) { }

  canActivate(): boolean {
    const time = this.verificaTime();

    if (isNaN(time) || time > 30) {
      this.route.navigate(['/auth']);
      return false;
    }

    return true;

  }

  verificaTime(): number {
    const now = new Date().getTime(); // Data de hoje

    const ant = parseFloat(localStorage.getItem('logged')) // Outra data no passado

    const diff = Math.abs(now - ant); // Subtrai uma data pela outra

    const minutes = Math.ceil(diff / (1000 * 60 * 60)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

    console.log(minutes);

    return minutes

  }
}
