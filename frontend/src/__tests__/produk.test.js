import { describe, it, expect } from 'vitest'
import { formatRupiah, getStockLabel } from '../utils/produk'

describe('formatRupiah', () => {
  it('memformat angka menjadi mata uang Rupiah', () => {
    const formatted = formatRupiah(15000000)
    expect(formatted).toContain('15.000.000')
  })

  it('mengembalikan "Rp 0" untuk nilai null atau tidak valid', () => {
    expect(formatRupiah(null)).toBe('Rp 0')
    expect(formatRupiah(undefined)).toBe('Rp 0')
  })
})

describe('getStockLabel', () => {
  it('mengembalikan "Habis" jika stok <= 0', () => {
    expect(getStockLabel(0)).toBe('Habis')
    expect(getStockLabel(-1)).toBe('Habis')
  })

  it('mengembalikan "Menipis" jika stok antara 1 dan 5', () => {
    expect(getStockLabel(3)).toBe('Menipis')
    expect(getStockLabel(5)).toBe('Menipis')
  })

  it('mengembalikan "Tersedia" jika stok > 5', () => {
    expect(getStockLabel(10)).toBe('Tersedia')
  })
})
