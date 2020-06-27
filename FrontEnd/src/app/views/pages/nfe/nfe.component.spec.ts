import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NfeComponent } from './nfe.component';

describe('NfeComponent', () => {
  let component: NfeComponent;
  let fixture: ComponentFixture<NfeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NfeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NfeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
