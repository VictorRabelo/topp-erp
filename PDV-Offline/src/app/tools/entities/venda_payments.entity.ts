import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'vendas_payments' })
export class VendaPayments extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: string;

	@Column('int', { nullable: true })
	venda_id: string;

	@Column('int', { nullable: true })
	forma_id: string;

	@Column('varchar')
	forma: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	valor: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	resto: string;

	@Column('varchar', { nullable: true })
	obs: string;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}