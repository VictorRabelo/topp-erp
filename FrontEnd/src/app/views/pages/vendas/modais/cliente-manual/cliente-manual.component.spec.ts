import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ClienteManualComponent } from './cliente-manual.component';

describe('ClienteManualComponent', () => {
  let component: ClienteManualComponent;
  let fixture: ComponentFixture<ClienteManualComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ClienteManualComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClienteManualComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
