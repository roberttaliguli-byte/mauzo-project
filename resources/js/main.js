
function mauzoApp() {
    return {
        // Sidebar & Tabs
        sidebarOpen: true,
        activeTab: 'sehemu',

        // Modals & customer data
        showMadeniModal: false,
        showKikapu: false,
        showKopesha: false,
        selectedMtejaId: '',
        selectedMteja: '',
        newMteja: '',
        wateja: @json($wateja),
        mteja: { jina: '', simu: '', barua_pepe: '', anapoishi: '' },
groupedMauzoByDate() {
    const grouped = {};
    this.mapatoList.forEach(item => {
        const date = item.created_at.split('T')[0]; // YYYY-MM-DD
        const key = date + '|' + item.bidhaa.jina; // group by date + product
        if (!grouped[key]) {
            grouped[key] = {
                tarehe: date,
                jina: item.bidhaa.jina,
                idadi: 0,
                jumla: 0,
                punguzo: 0,
                faida: 0,
            };
        }
        grouped[key].idadi += item.idadi;
        grouped[key].jumla += parseFloat(item.jumla);
        grouped[key].punguzo += parseFloat(item.punguzo || 0);
        const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
        const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
        grouped[key].faida += (beiKuuza - beiNunua) * item.idadi;
    });
    return Object.values(grouped).sort((a, b) => b.tarehe.localeCompare(a.tarehe));
    
}, // ✅ COMMA added

        // Fill customer details
        fillMtejaDetails() {
            const select = document.querySelector('select[x-model="selectedMtejaId"]');
            const option = select?.options[select.selectedIndex];
            if (!option || !option.value) {
                this.mteja = { jina: '', simu: '', barua_pepe: '', anapoishi: '' };
                return;
            }
            this.mteja = {
                jina: option.dataset.jina || '',
                simu: option.dataset.simu || '',
                barua_pepe: option.dataset.barua_pepe || '',
                anapoishi: option.dataset.anapoishi || '',
            };
        },

        // --- Kikapu (Cart) ---
        cart: [],

        addToCart() {
            if (!this.bidhaaSelected || this.idadi < 1) {
                alert('Tafadhali chagua bidhaa na idadi sahihi!');
                return;
            }

            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            if (!product) return alert('Bidhaa haipo!');

            this.cart.push({
                jina: product.jina,
                bei: this.bei,
                idadi: this.idadi,
                punguzo: this.punguzoCheck ? this.punguzo : 0,
                faida: (this.bei - (product.bei_nunua || 0)) * this.idadi,
                muda: new Date().toLocaleTimeString(),
            });

            this.bidhaaSelected = '';
            this.idadi = 1;
            this.punguzo = 0;
            alert('Bidhaa imeongezwa kwenye kikapu!');
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Una uhakika unataka kufuta bidhaa zote kwenye kikapu?')) {
                this.cart = [];
            }
        },

        cartTotal() {
            return this.cart.reduce(
                (sum, item) => sum + (item.bei * item.idadi - item.punguzo),
                0
            ).toLocaleString() + '/=';
        },

        // --- Mauzo ya Kikapu ---
        checkoutCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');

            fetch("{{ route('mauzo.store.kikapu') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Mauzo yamehifadhiwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kuhifadhi mauzo ya kikapu!'));
        },

        // --- Kopesha (Loan Sale) ---
        openKopeshaModal() {
            if (this.cart.length === 0) {
                alert('Hakuna bidhaa kwenye kikapu.');
                return;
            }
            this.showKopesha = true;
        },

submitLoan() {
    if (!this.selectedMteja) {
        alert('Tafadhali chagua mteja.');
        return;
    }

    fetch("{{ route('mauzo.store.kopesha') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            jina: this.selectedMteja,
            items: this.cart
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message || 'Mauzo yamekopeshwa kwa mafanikio!');
            this.cart = [];
            this.showKopesha = false;
            this.showKikapu = false;
            // 🔁 Refresh page automatically
            window.location.reload();
        } else {
            alert('Kuna tatizo: ' + (data.message || 'Jaribu tena.'));
        }
    })
    .catch(err => console.error(err));
},


        kopeshaCart() {
            if (this.cart.length === 0) return alert('Kikapu hakina bidhaa!');
            const jina = prompt('Ingiza jina la mteja anayekopeshwa:');
            if (!jina) return;

            fetch("{{ route('mauzo.store.kopesha') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ jina, items: this.cart })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Bidhaa zimekopeshwa kwa mafanikio!');
                this.cart = [];
                this.showKikapu = false;
                window.location.reload();
            })
            .catch(() => alert('Kuna tatizo kwenye kukopesha bidhaa!'));
        },


        // --- Sehemu ya Mauzo ---
        bidhaaSelected: '',
        idadi: 1,
        punguzo: 0,
        punguzoCheck: false,
        stock: 0,
        mapatoList: @json($mauzos),
        matumiziList: @json($matumizi),
        bidhaaList: @json($bidhaa),
        today: "{{ \Carbon\Carbon::today()->format('Y-m-d') }}",

        updateStock() {
            if (!this.bidhaaSelected) {
                this.stock = 0;
                return;
            }
            const product = this.bidhaaList.find(b => b.id == this.bidhaaSelected);
            this.stock = product ? product.idadi : 0;
            if (this.idadi > this.stock) this.idadi = this.stock;
        },

        get bei() {
            if (!this.bidhaaSelected) return 0;
            const option = document.querySelector(
                `select[name=\"bidhaa_id\"] option[value='${this.bidhaaSelected}']`
            );
            return parseFloat(option?.dataset.bei) || 0;
        },

        get jumla() {
            return (this.idadi * this.bei) - (this.punguzoCheck ? this.punguzo : 0);
        },

        decreaseStock() {
            if (this.idadi > this.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                return false;
            }
            this.stock -= this.idadi;
            return true;
        },

        // --- Barcode Mauzo ---
        barcodeCart: [{ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 }],

        addBarcodeRow() {
            this.barcodeCart.push({ barcode: '', jina: '', bei: 0, idadi: 1, stock: 0, jumla: 0 });
        },

        fetchBidhaa(item, index) {
            if (!item || !item.barcode) return;
            const product = this.bidhaaList.find(b => b.barcode === item.barcode);
            if (!product) {
                alert('Bidhaa haipatikani kwa barcode hii!');
                Object.assign(item, { jina: '', bei: 0, stock: 0, idadi: 1, jumla: 0 });
                return;
            }
            item.jina = product.jina;
            item.bei = parseFloat(product.bei_kuuza);
            item.stock = parseInt(product.idadi);
            item.idadi = 1;
            item.jumla = item.bei * item.idadi;
            if (index === this.barcodeCart.length - 1) this.addBarcodeRow();
        },

        updateJumla(item) {
            if (item.idadi > item.stock) {
                alert('Idadi uliyoiingiza inazidi idadi iliyopo!');
                item.idadi = item.stock;
            }
            item.jumla = item.bei * item.idadi;
        },

        removeItem(index) {
            this.barcodeCart.splice(index, 1);
        },

        barcodeCartTotal() {
            return this.barcodeCart
                .reduce((sum, item) => sum + parseFloat(item.jumla || 0), 0)
                .toLocaleString() + '/=';
        },

        submitBarcodeMauzo() {
            const payload = this.barcodeCart
                .filter(item => item.barcode)
                .map(item => ({
                    barcode: item.barcode,
                    idadi: item.idadi,
                    punguzo: 0
                }));

            if (payload.length === 0) {
                alert('Tafadhali ingiza angalau barcode moja!');
                return;
            }

            fetch("{{ route('mauzo.store.barcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({ items: payload })
            })
            .then(async res => {
                if (!res.ok) {
                    const errData = await res.json().catch(() => ({ message: 'Kuna tatizo kwenye uhifadhi!' }));
                    throw errData;
                }
                return res.json();
            })
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Kuna tatizo kwenye uhifadhi!');
            });
        },

        // --- Summary Functions ---
        filterToday(list) {
            return list.filter(item => item.created_at?.startsWith(this.today));
        },

        totalMapato() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        },
        totalMatumizi() {
            return this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0)
                .toLocaleString() + '/=';
        },
        totalFaida() {
            return this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0)
                .toLocaleString() + '/=';
        },
        totalFedhaLeo() {
            const mapato = this.filterToday(this.mapatoList)
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (mapato - matumizi).toLocaleString() + '/=';
        },
        totalFaidaHalisi() {
            const faida = this.filterToday(this.mapatoList)
                .reduce((sum, item) => {
                    const beiKuuza = parseFloat(item.bidhaa.bei_kuuza || 0);
                    const beiNunua = parseFloat(item.bidhaa.bei_nunua || 0);
                    return sum + ((beiKuuza - beiNunua) * item.idadi);
                }, 0);
            const matumizi = this.filterToday(this.matumiziList)
                .reduce((sum, item) => sum + parseFloat(item.gharama), 0);
            return (faida - matumizi).toLocaleString() + '/=';
        },
        totalJumlaKuu() {
            return this.mapatoList
                .reduce((sum, item) => sum + parseFloat(item.jumla), 0)
                .toLocaleString() + '/=';
        }
    };
    
}


