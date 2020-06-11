import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EmitenteConfigFormComponent } from './emitente-config-form.component';

describe('EmitenteConfigFormComponent', () => {
  let component: EmitenteConfigFormComponent;
  let fixture: ComponentFixture<EmitenteConfigFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EmitenteConfigFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EmitenteConfigFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
