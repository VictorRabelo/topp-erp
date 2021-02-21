import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'vendas_itens' })
export class VendaItens extends BaseEntity {

	@PrimaryGeneratedColumn()
	id;

	@Column('int', { nullable: true })
	venda_id;

	@Column('int', { nullable: true })
	produto_id;

	@Column('varchar')
	descricao;

	@Column('float', { default: 0, precision: 10, scale: 3 })
	quantidade;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	valor_unitario;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	desconto;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	total;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}