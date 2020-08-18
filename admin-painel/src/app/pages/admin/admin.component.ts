import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']
})
export class AdminComponent implements OnInit {

  menu = [
    { title: 'Dashboad', icon: 'fas fa-columns', route: '/admin/home' },
    { title: 'Empresas', icon: 'fas fa-users', route: '/admin/busness' }
  ]

  constructor(
    private route: Router
  ) { }

  ngOnInit(): void {
  }

  logout() {
    localStorage.removeItem('logged');
    this.route.navigate(['/auth']);
  }

}
