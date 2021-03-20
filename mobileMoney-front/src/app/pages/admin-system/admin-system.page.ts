import { Component, OnInit } from '@angular/core';
import {AdminPages} from '../../utils/pagesUrl';
import {Router} from '@angular/router';

@Component({
  selector: 'app-admin-system',
  templateUrl: './admin-system.page.html',
  styleUrls: ['./admin-system.page.scss'],
})
export class AdminSystemPage implements OnInit {
pages: any = [];
  constructor(private router: Router) {
    this.pages = AdminPages;
  }

  ngOnInit() {
  }

  onselected(url) {
    this.router.navigateByUrl(url);
  }
}
