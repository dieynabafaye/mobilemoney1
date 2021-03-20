import { Component } from '@angular/core';
import { Router, RouterEvent } from '@angular/router';
import { pages } from './utils/pagesUrl';
import {AuthService} from './services/auth.service';
@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent {
  pages: any = [];

  public selectedPath = '';

  constructor(private router: Router, private authService: AuthService) {
    this.pages = pages;
    this.router.events.subscribe((event: RouterEvent) => {
      if (event && event.url) {
        this.selectedPath = event.url;
      }
    });
  }

  ngOnInit() {}

  onItemClick(url: string) {
    this.router.navigate([url]);
  }
  logOut() {
    this.authService.logout();
    this.router.navigateByUrl('/');
  }
}
