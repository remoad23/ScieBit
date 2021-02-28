import {Component, Input, ViewEncapsulation} from '@angular/core';

@Component({
  selector: 'interaction',
  template: `
    <div class="interactionWrapper" >
      <div *ngIf="MouseMenu" [class]="iconClass" class="interactionIcon" ></div>
      <a *ngIf="urlPassed" id="linkFile" [href]="urlPassed" i18n>Download</a>
      <p *ngIf="!urlPassed" >{{text}}</p>
    </div>
  `,
  styles: [`
    interaction
    {
      height: 100%;
      width: 100%;
      border-bottom: 1px solid white;
      display: flex;
    }
    .interactionWrapper
    {
      width: 100%;
      display: flex;
      flex-flow: row;
      align-items: center;
    }

    .interactionWrapper > p
    {
      margin: 0;
      padding: 0;
    }
    .interactionIcon
    {
      align-self: center;
      width: 20%;
      height: 100%;
      background-size: 40%;
    }
    a{
      text-decoration: none;
      color: white;
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class InteractionComponent
{

  // text what will be seen in the interaction
  @Input() text: string = "Placeholder";
  // css icon Class
  @Input() iconClass: string = "";
  @Input() MouseMenu: boolean = true;

  @Input() urlPassed: string = "";


}
