import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FiscalSendXmlComponent } from './fiscal-send-xml.component';

describe('FiscalSendXmlComponent', () => {
  let component: FiscalSendXmlComponent;
  let fixture: ComponentFixture<FiscalSendXmlComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FiscalSendXmlComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FiscalSendXmlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
