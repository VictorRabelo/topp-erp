import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EmitentesComponent } from './emitentes.component';

describe('EmitentesComponent', () => {
  let component: EmitentesComponent;
  let fixture: ComponentFixture<EmitentesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EmitentesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EmitentesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
