import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EmitenteCertificateComponent } from './emitente-certificate.component';

describe('EmitenteCertificateComponent', () => {
  let component: EmitenteCertificateComponent;
  let fixture: ComponentFixture<EmitenteCertificateComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EmitenteCertificateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EmitenteCertificateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
