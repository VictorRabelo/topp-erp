import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'vendas' })
export class Venda extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: string;

	@Column('int', { nullable: true })
	empresa_id: string;

	@Column('int')
	user_id: number;

	@Column('int', { nullable: true })
	cliente_id: number;

	@Column('varchar', { default: 'Consumidor Final' })
	cliente: string;

	@Column('varchar', { default: '' })
	cpf: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	subtotal: number;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	desconto: number;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	total: number;

	@Column('int', { default: 1 })
	status: number;

	@Column('tinyint', { default: 0 })
	sync: boolean;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}