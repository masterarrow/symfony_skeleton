export function formatMoney(value: number) {
  if (value === null || value === undefined) {
    return ''
  }

  const options: Intl.NumberFormatOptions = {
    style: 'currency',
    currency: 'USD',
  }

  return new Intl.NumberFormat('en-US', options).format(value)
}
