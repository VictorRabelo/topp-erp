import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NotaEntradaComponent } from './nota-entrada.component';

describe('NotaEntradaComponent', () => {
  let component: NotaEntradaComponent;
  let fixture: ComponentFixture<NotaEntradaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NotaEntradaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NotaEntradaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
