/**
 * Format angka menjadi format Rupiah Indonesia.
 */
export const formatRupiah = (number) => {
  if (number === null || number === undefined || isNaN(number)) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(number)
}

/**
 * Format status stok berdasarkan kuantitas.
 */
export const getStockLabel = (stock) => {
  if (stock <= 0) return 'Habis'
  if (stock <= 5) return 'Menipis'
  return 'Tersedia'
}
