import { Component, OnInit } from '@angular/core';
import {Router} from '@angular/router';
import {INTRO_KEY} from '../guards/redirect.guard';
import { Plugins} from '@capacitor/core';
const { Storage } = Plugins;

@Component({
  selector: 'app-accueil',
  templateUrl: './accueil.page.html',
  styleUrls: ['./accueil.page.scss'],
})
export class AccueilPage implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }

  async direct() {
    await Storage.set({key: INTRO_KEY, value: 'true'});
    this.router.navigateByUrl('/login');
  }
}
