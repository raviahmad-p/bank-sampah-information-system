/* ===============================
  Dashboard Admin
================================ */
document.addEventListener("DOMContentLoaded", function () {

    const data = window.dashboardData;
    if (!data) return;

    new Chart(document.getElementById('chartBulanan'),{
        type:'bar',
        data:{
            labels:data.bulan,
            datasets:[{
                data:data.kgBulanan,
                backgroundColor:'#4ade80',
                borderRadius:6,
                barThickness:18,
                maxBarThickness:22
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{
                x:{ticks:{font:{size:10}}},
                y:{beginAtZero:true,ticks:{font:{size:10}}}
            }
        }
    });

    new Chart(document.getElementById('chartJenis'),{
        type:'doughnut',
        data:{
            labels:data.jenis,
            datasets:[{
                data:data.kgJenis,
                backgroundColor:[
                    '#4ade80','#60a5fa','#facc15',
                    '#f87171','#c084fc'
                ]
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{legend:{position:'bottom'}}
        }
    });

});
/* ===============================
  Data_Nasabah
================================ */
/* ================= KONFIRMASI HAPUS ================= */
function confirmDelete(){
    return confirm('Yakin hapus data ini?');
}
/* ===============================
Jenis_Sampah
================================ */
function openModal(){
    modal.style.display='flex';
    modalTitle.innerText='Tambah Jenis Sampah';
    id.value=''; nama.value=''; harga.value='';
}
function closeModal(){
    modal.style.display='none';
}
function editData(idv, namav, hargav){
    openModal();
    modalTitle.innerText='Edit Jenis Sampah';
    id.value=idv;
    nama.value=namav;
    harga.value=hargav;
}
window.onclick=e=>{
    if(e.target===modal) closeModal();
}

