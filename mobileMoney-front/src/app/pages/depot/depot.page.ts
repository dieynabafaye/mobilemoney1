import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormControl, FormGroup, Validators} from '@angular/forms';
import {AlertController, LoadingController} from '@ionic/angular';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-depot',
  templateUrl: './depot.page.html',
  styleUrls: ['./depot.page.scss'],
})
export class DepotPage implements OnInit {
  visible = true;
  position = 'emetteur';
  form: FormGroup;
  frais : any;
  totalMontant: any;
  montantEnvoi : any;

  constructor(private fb: FormBuilder,
              private  alertCtrl: AlertController,
              private loadingCtrl: LoadingController,
              private authService: AuthService) { }

  ngOnInit() {
    this.form = this.fb.group({
      montant: [ [Validators.required, Validators.min(500)]],
      status: [true, []],
      type: ['depot', []],
      total: [ [Validators.min(500)]],
      clientenvoi:  this.fb.group({
        nom: ['FAYE', [Validators.required, Validators.minLength(2)]],
        prenom: ['Jeyna', [Validators.required, Validators.minLength(2)]],
        CNI: ['1234567898765', [Validators.required, Validators.minLength(13), Validators.maxLength(13)]],
        tel: ['777777777', [Validators.required, Validators.minLength(2)]],
      }),
      clientRetrait:  this.fb.group({
        nom: ['FAYE', [Validators.required, Validators.minLength(2)]],
        prenom: ['Jeyna', [Validators.required, Validators.minLength(2)]],
        tel: ['777777777', [Validators.required, Validators.minLength(2)]],
      }),
    });
  }
  preview() {
    this.visible = true;
  }
  next() {
    this.visible = false;
    this.position = 'beneficiaire';
  }

  async addDepot() {
    console.log(this.form.value);
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirm!',
      message: `<div class="affiche">
                  Emetteur  <br> <p>${this.form.value.clientenvoi.prenom} ${this.form.value.clientenvoi.nom}</p> <br>
                  Téléphone  <br><p>${this.form.value.clientenvoi.tel}</p><br>
                  N CNI  <br><p>${this.form.value.clientenvoi.CNI}</p><br>
                  Récepteur  <br><p>${this.form.value.clientRetrait.prenom} ${this.form.value.clientRetrait.nom}</p><br>
                  Montant  <br><p>${this.form.value.montant}</p> <br>
                  Téléphone  <br><p>${this.form.value.clientRetrait.tel}</p><br>
                </div>`,
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel',
          cssClass: 'secondary',
          handler: (blah) => {
          }
        }, {
          text: 'Confirmer',
          handler: async () => {
            const loading = await this.loadingCtrl.create({
              message: 'Please wait...'
            });
            await loading.present();

            this.authService.Transaction(this.form.value).subscribe(
              async (response) => {
                console.log(response);
                const result = response.data;
                await loading.dismiss();
                const sms = await this.alertCtrl.create({
                  cssClass: 'my-custom-class',
                  header: 'Transfère réussie',
                  message: `<ion-card>
                              <ion-item >
                              <ion-label class="ion-text-wrap">
                                   Vous avez envoyé ${result.montant} à  ${result.clientRetrait.nomComplet} le ${result.dateEnvoi}
                               </ion-label>
                              </ion-item>
                                <ion-item>
                                 <ion-label>Code de transaction</ion-label>
                                </ion-item>
                                <ion-item>${result.code} </ion-item>
                              </ion-card-content>
                            </ion-card>`,
                  buttons: ['OK']
                });

                await sms.present();
              },
              async (error) => {
                console.log(error);
                await loading.dismiss();
                const sms = await this.alertCtrl.create({
                  cssClass: 'my-custom-class',
                  header: 'Erreur',
                  subHeader: 'Subtitle',
                  message: 'Transaction échoué.',
                  buttons: ['OK']
                });

                await sms.present();
              });
          }
        }
      ]
    });

    await alert.present();
  }

  decalculFrais(event: KeyboardEvent) {
    if (this.form.value.total === 0 || this.form.value.total < 500 || this.form.value.total == null){
      this.frais = null;
      this.montantEnvoi = null;
    }else {
      this.authService.deCalculator(this.form.value).subscribe(
        async (res) => {
          this.frais = res.data.frais;
          this.montantEnvoi = res.data.montantEnvoi;
        }, async (error) => {
        });
    }
  }
  calculFrais(event: KeyboardEvent) {
    if (this.form.value.montant === 0 || this.form.value.montant < 500 || this.form.value.montant == null){
      this.frais = null;
      this.totalMontant = null;
    }else {
      this.authService.calculator(this.form.value).subscribe(
        async (res) => {
          this.frais = res.data;
          this.totalMontant = res.data + this.form.value.montant;
        }, async (error) => {
        });
    }
  }

}
